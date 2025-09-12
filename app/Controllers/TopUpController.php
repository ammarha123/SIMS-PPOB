<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\NutechAPI;
use CodeIgniter\HTTP\ReeponseInterface;

class TopUpController extends BaseController
{
    public function index()
    {
        if (! session('auth')) {
            return redirect()->to('/login');
        }

        $name        = '';
        $avatar      = '';
        $token       = session('auth_token');
        $saldoHidden = true;

        $balance  = 0;
        if ($token) {
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
                if ($ok($rb)) {
                    $balance = (int) ($rb['body']['data']['balance'] ?? 0);
                }
            } catch (\Throwable $e) {
            }
        }
        return view('topup/index', [
            'title' => 'Top Up',
            'name'        => $name,
            'avatar'      => $avatar,
            'saldoHidden' => $saldoHidden,
            'balance'     => $balance,
        ]);
    }

    public function store()
    {
        if (! session('auth')) return redirect()->to('/login');

        $raw    = (string) $this->request->getPost('nominal');
        $amount = (int) preg_replace('/\D+/', '', $raw); // ambil angka murni

        $errors = [];
        if ($amount < 10000) {
            $errors['nominal'] = 'Gagal, minimal isi saldo Rp10.000.';
        } elseif ($amount > 1000000) {
            $errors['nominal'] = 'Gagal, maksimum nominal Rp1.000.000.';
        }

        if ($errors) {
            session()->setFlashdata('topup_result', [
                'ok'     => false,
                'amount' => $amount,
                'message' => $errors['nominal'],
            ]);
            return redirect()->to('/topup')->withInput();
        }

        $token = (string) session('auth_token');
        if ($token === '') return redirect()->to('/login');

        try {
            $api = new NutechAPI();
            $response = $api->topup($token, $amount);
        } catch (\Throwable $e) {
            session()->setFlashdata('topup_result', [
                'ok'     => false,
                'amount' => $amount,
                'message' => 'Gagal menghubungi server.',
            ]);
            return redirect()->to('/topup')->withInput();
        }

        $http = (int) ($response['status'] ?? 0);
        $body = $response['body'] ?? [];
        $ok   = ($http >= 200 && $http < 300) && ((int) ($body['status'] ?? 1) === 0);

        if (! $ok) {
            session()->setFlashdata('topup_result', [
                'ok'     => false,
                'amount' => $amount,
                'message' => $body['message'] ?? 'Top Up gagal.',
            ]);
            return redirect()->to('/topup');
        }

        // sukses: update saldo cache bila dikembalikan API
        $newBalance = (int) ($body['data']['balance'] ?? 0);
        if ($newBalance > 0) {
            session()->set('cached_balance', $newBalance);
            session()->set('cached_balance_exp', time() + 30);
        }

        session()->setFlashdata('topup_result', [
            'ok'     => true,
            'amount' => $amount,
            'message' => $body['message'] ?? 'Top Up berhasil.',
        ]);
        return redirect()->to('/topup');
    }
}
