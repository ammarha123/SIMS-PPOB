<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\NutechAPI;

class Home extends BaseController
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
        $services = [];
        $banners  = [];

        if ($token) {
            try {
                $api = new NutechAPI();
                $ok  = static fn(array $r): bool =>
                    (($r['status'] ?? 0) >= 200 && ($r['status'] ?? 0) < 300) && (($r['body']['status'] ?? 1) === 0);

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

                $rs = $api->services($token);
                if ($ok($rs)) {
                    $services = $rs['body']['data'] ?? [];
                }

                $rn = $api->banners($token);
                if ($ok($rn)) {
                    $banners = $rn['body']['data'] ?? [];
                }
            } catch (\Throwable $e) {
            }
        }

        return view('index', [
            'title'       => 'Home Page',
            'name'        => $name,
            'avatar'      => $avatar,
            'saldoHidden' => $saldoHidden,
            'balance'     => $balance,
            'services'    => $services,
            'banners'     => $banners,
        ]);
    }
}
