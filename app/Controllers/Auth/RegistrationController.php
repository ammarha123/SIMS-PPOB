<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Libraries\NutechAPI;

class RegistrationController extends BaseController
{
    public function index()
    {
        return view('auth/register', [
            'title' => 'Registrasi SIMS PPOB'
        ]);
    }

    public function submit()
    {
        $rules = [
            'email'            => 'required|valid_email',
            'first_name'         => 'required|min_length[3]|max_length[50]',
            'last_name'             => 'required|min_length[3]|max_length[100]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $payload = [
            'email'    => trim((string) $this->request->getPost('email')),
            'first_name' => trim((string) $this->request->getPost('first_name')),
            'last_name'     => trim((string) $this->request->getPost('last_name')),
            'password' => (string) $this->request->getPost('password'),
        ];

        try {
            $api = new NutechAPI();
            $res = $api->register($payload);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('errors', [
                'api' => 'Konfigurasi/API error: ' . $e->getMessage()
            ]);
        }
        $status = (int) ($res['status'] ?? 0);
        $body   = $res['body'] ?? [];
        $msg    = is_array($body) ? ($body['message'] ?? 'Registrasi berhasil silahkan login.') : 'Registrasi berhasil silahkan login.';

        $okFlag = true;
        if (is_array($body) && array_key_exists('status', $body)) {
            $okFlag = ((int) $body['status'] === 0);
        }
        $apiOk = ($status >= 200 && $status < 300) && $okFlag;

        if ($apiOk) {
            return redirect()->to('/registration')->with('success', $msg);
        }

        $errors = [];
        if (is_array($body) && isset($body['errors']) && is_array($body['errors'])) {
            foreach ($body['errors'] as $k => $v) {
                $errors[$k] = is_array($v) ? implode(', ', $v) : $v;
            }
        }
        if (!$errors) {
            $errors['api'] = $msg;
        }
        return redirect()->back()->withInput()->with('errors', $errors);
    }
}