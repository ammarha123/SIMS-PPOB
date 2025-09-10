<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Libraries\NutechAPI;

class LoginController extends BaseController
{
    public function index()
    {
        return view('auth/login', [
            'title' => 'Login ke SIMS PPOB'
        ]);
    }

    public function submit()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = [
            'email'    => trim((string) $this->request->getPost('email')),
            'password' => (string) $this->request->getPost('password'),
        ];

        try {
            $api = new NutechAPI();
            $res = $api->login($payload);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('errors', ['api' => 'Konfigurasi/API error: ' . $e->getMessage()]);
        }

        $status = (int) ($res['status'] ?? 0);
        $body   = $res['body'] ?? [];
        $msg    = $body['message'] ?? 'Login gagal.';

        $okFlag = true;
        if (is_array($body) && array_key_exists('status', $body)) {
            $okFlag = ((int) $body['status'] === 0);
        }
        $apiOk = ($status >= 200 && $status < 300) && $okFlag;

        if ($apiOk) {
            $data = $body['data'] ?? [];
            session()->set([
                'auth'         => true,
                'auth_email'   => $payload['email'],
                'auth_token'   => $data['token'] ?? null,
                'auth_profile' => $data['profile'] ?? null,
            ]);
            return redirect()->to('/')->with('success', 'Login berhasil.');
        }

        return redirect()->back()->withInput()->with('errors', ['api' => $msg]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Berhasil logout.');
    }
}
