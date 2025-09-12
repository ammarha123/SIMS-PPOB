<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\NutechAPI;

class PaymentController extends BaseController
{
    public function index(string $serviceCode)
    {
        if (! session('auth')) return redirect()->to('/login');

        $token       = session('auth_token');
        $name        = session('auth_name');
        $avatar      = session('auth_avatar');
        $saldoHidden = true;
        $balance     = 0;
        $service     = null;

        try {
            $api = new NutechAPI();
            $ok  = static fn(array $r): bool => (($r['status'] ?? 0) >= 200 && ($r['status'] ?? 0) < 300) && (($r['body']['status'] ?? 1) === 0);

            $rp = $api->profile($token);
            if ($ok($rp)) {
                $d = $rp['body']['data'] ?? [];
                if (!empty($d['profile_image'])) {
                    $avatar = $d['profile_image'];
                }
                $fullName = trim(($d['first_name'] ?? '') . ' ' . ($d['last_name'] ?? ''));
                if ($fullName !== '') {
                    $name = $fullName;
                }
            }
            
            $rb = $api->balance($token);
            if (($rb['status'] ?? 0) >= 200 && ($rb['status'] ?? 0) < 300 && (($rb['body']['status'] ?? 1) === 0)) {
                $balance = (int) ($rb['body']['data']['balance'] ?? 0);
            }

            $rs = $api->services($token);
            $ok = (($rs['status'] ?? 0) >= 200 && ($rs['status'] ?? 0) < 300) && (($rs['body']['status'] ?? 1) === 0);
            if ($ok) {
                foreach ($rs['body']['data'] as $s) {
                    if (($s['service_code'] ?? null) === $serviceCode) {
                        $service = [
                            'code'   => $s['service_code'],
                            'name'   => $s['service_name'] ?? 'Service',
                            'icon'   => $s['service_icon'] ?? null,
                            'tariff' => (int) ($s['service_tariff'] ?? 0),
                        ];
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        if (! $service) return redirect()->to('/')->with('errors', ['api' => 'Layanan tidak ditemukan.']);

        return view('payment/index', [
            'title'       => 'Pembayaran - ' . $service['name'],
            'name'        => $name,
            'avatar'      => $avatar,
            'balance'     => $balance,
            'saldoHidden' => $saldoHidden,
            'service'     => $service,
        ]);
    }

    public function pay(string $serviceCode)
    {
        if (! session('auth')) return redirect()->to('/login');

        $token  = session('auth_token');
        $amount = (int) preg_replace('/\D+/', '', (string) $this->request->getPost('amount'));

        $tariff = 0;
        $serviceName = $serviceCode;
        $balance = 0;

        try {
            $api = new NutechAPI();
           
            $rs = $api->services($token);
            $ok = (($rs['status'] ?? 0) >= 200 && ($rs['status'] ?? 0) < 300) && (($rs['body']['status'] ?? 1) === 0);
            if ($ok) {
                foreach ($rs['body']['data'] as $s) {
                    if (($s['service_code'] ?? null) === $serviceCode) {
                        $tariff      = (int) ($s['service_tariff'] ?? 0);
                        $serviceName = $s['service_name'] ?? $serviceCode;
                        break;
                    }
                }
            }

            $rb = $api->balance($token);
            if (($rb['status'] ?? 0) >= 200 && ($rb['status'] ?? 0) < 300 && (($rb['body']['status'] ?? 1) === 0)) {
                $balance = (int)($rb['body']['data']['balance'] ?? 0);
            }
        } catch (\Throwable $e) {
            return redirect()->to('/payment/' . $serviceCode)
                ->withInput()
                ->with('errors', ['api' => 'Gagal memuat data.']);
        }

        $charge = $tariff > 0 ? $tariff : $amount;
        if ($charge <= 0) {
            return redirect()->to('/payment/' . $serviceCode)
                ->withInput()
                ->with('errors', ['form' => 'Nominal tidak valid.']);
        }
        if ($balance < $charge) {
            return redirect()->to('/payment/' . $serviceCode)
                ->withInput()
                ->with('errors', ['form' => 'Saldo tidak mencukupi.']);
        }

        try {
            $api = new NutechAPI();
            $response = $api->transaction($token, $serviceCode);
        } catch (\Throwable $e) {
            return redirect()->to('/payment/' . $serviceCode)
                ->withInput()
                ->with('errors', ['api' => 'Gagal memproses transaksi.']);
        }

        $http = (int) ($response['status'] ?? 0);
        $body = $response['body'] ?? [];
        $ok   = ($http >= 200 && $http < 300) && ((int) ($body['status'] ?? 1) === 0);

        if (! $ok) {
            return redirect()->to('/payment/' . $serviceCode)
                ->withInput()
                ->with('errors', ['api' => $body['message'] ?? 'Transaksi gagal.']);
        }
        return redirect()->to('/payment/' . $serviceCode)
            ->with('pay_result', [
                'ok'      => true,
                'amount'  => $charge,
                'message' => $body['message'] ?? 'Transaksi berhasil.',
                'data'    => $body['data'] ?? null,
            ]);
    }
}
