<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\NutechAPI;

class TransactionController extends BaseController
{
    public function index()
    {
        if (! session('auth')) return redirect()->to('/login');

        $token  = session('auth_token');
        $name   = '';
        $avatar = '';

        $limit  = 5;
        $offset = 0;

        $balance     = 0;
        $saldoHidden = true;
        $items       = [];

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

            $rh = $api->history($token, $offset, $limit);
            $ok = (($rh['status'] ?? 0) >= 200 && ($rh['status'] ?? 0) < 300) && (($rh['body']['status'] ?? 1) === 0);
            if ($ok) {
                foreach (($rh['body']['data']['records'] ?? []) as $r) {
                    $items[] = [
                        'invoice' => $r['invoice_number']    ?? '-',
                        'type'    => $r['transaction_type']  ?? '-',
                        'name'    => $r['description']       ?? '-',
                        'amount'  => (int) ($r['total_amount'] ?? 0),
                        'time'    => isset($r['created_on']) ? date('d M Y H:i', strtotime($r['created_on'])) : '-',
                    ];
                }
            }
        } catch (\Throwable $e) {}

        return view('transaction/index', [
            'title'       => 'Transaction',
            'name'        => $name,
            'avatar'      => $avatar,
            'balance'     => $balance,
            'saldoHidden' => $saldoHidden,
            'items'       => $items,
            'limit'       => $limit,
            'nextOffset'  => $limit,
            'hasMore'     => count($items) >= $limit,
        ]);
    }

    public function more()
    {
        if (! session('auth')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'unauthorized']);
        }

        $token  = session('auth_token');
        $limit  = max(1, (int) ($this->request->getGet('limit') ?? 5));
        $offset = max(0, (int) ($this->request->getGet('offset') ?? 0));

        $items = [];
        try {
            $api = new NutechAPI();
            $rh  = $api->history($token, $offset, $limit);
            $ok  = (($rh['status'] ?? 0) >= 200 && ($rh['status'] ?? 0) < 300) && (($rh['body']['status'] ?? 1) === 0);
            
            if ($ok) {
                foreach (($rh['body']['data']['records'] ?? []) as $r) {
                    $items[] = [
                        'invoice' => $r['invoice_number']    ?? '-',
                        'type'    => $r['transaction_type']  ?? '-',
                        'name'    => $r['description']       ?? '-',
                        'amount'  => (int) ($r['total_amount'] ?? 0),
                        'time'    => isset($r['created_on']) ? date('d M Y H:i', strtotime($r['created_on'])) : '-',
                    ];
                }
            }
        } catch (\Throwable $e) {}

        $hasMore   = count($items) >= $limit;
        $nextOffset = $offset + $limit;

        return $this->response->setJSON([
            'items'      => $items,
            'hasMore'    => $hasMore,
            'nextOffset' => $nextOffset,
        ]);
    }
}
