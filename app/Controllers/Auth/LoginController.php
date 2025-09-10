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
            'password' => 'required|min_length[8]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        try {
            $api   = new NutechAPI();
            $login = $api->login(['email' => $email, 'password' => $password]);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('errors', ['api' => 'API error']);
        }

        $lb   = $login['body'] ?? [];
        $ok   = ((int) ($login['status'] ?? 0) >= 200 && (int) ($login['status'] ?? 0) < 300) && ((int) ($lb['status'] ?? 1) === 0);

        if (! $ok) {
            return redirect()->back()->withInput()->with('errors', ['api' => 'Login gagal']);
        }
        $ldata = $lb['data'] ?? [];
        $token = $ldata['token'] ?? null;
        $authEmail  = $ldata['email'] ?? $email;
        $authName   = '';
        $authProfilImage = '';

        if ($token) {
            $prof = $api->profile($token);
            $pOk  = ((int) ($prof['status'] ?? 0) >= 200 && (int) ($prof['status'] ?? 0) < 300) && ((int) ($prof['body']['status'] ?? 1) === 0);
            if ($pOk) {
                $profile = $prof['body']['data'] ?? [];
                $first_name = trim((string) ($profile['first_name'] ?? ''));
                $last_name = trim((string) ($profile['last_name'] ?? ''));
                $authName = trim($first_name . ' ' . $last_name);
                if (!empty($profile['profile_image'])) {
                    $authProfilImage = $profile['profile_image'];
                }
                if (!empty($profile['email'])) {
                    $authEmail = $profile['email'];
                }
            }
        }
        session()->set([
            'auth'        => true,
            'auth_email'  => $authEmail,
            'auth_name'   => $authName,
            'auth_profil_image' => $authProfilImage,
            'auth_token'  => $token,
        ]);

        return redirect()->to('/')->with('success', 'Login berhasil.');
    }


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Berhasil logout.');
    }
}
