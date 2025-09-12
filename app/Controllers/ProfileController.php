<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\NutechAPI;

class ProfileController extends BaseController
{
    public function index()
    {
        if (! session('auth')) return redirect()->to('/login');

        $token  = session('auth_token');
        $name   = session('auth_name');
        $email  = session('auth_email');
        $avatar = session('auth_avatar') ?: base_url('img/default-avatar.png');
        $first  = '';
        $last   = '';

        try {
            $api  = new NutechAPI();
            $response  = $api->profile($token);
            $ok   = (($response['status'] ?? 0) >= 200 && ($response['status'] ?? 0) < 300) && (($response['body']['status'] ?? 1) === 0);
            if ($ok) {
                $d      = $response['body']['data'] ?? [];
                $email  = $d['email']         ?? $email;
                $first  = $d['first_name']    ?? '';
                $last   = $d['last_name']     ?? '';
                $avatar = $d['profile_image'] ?: $avatar;
                session()->set([
                    'auth_email'  => $email,
                    'auth_name'   => trim(($d['first_name'] ?? '').' '.($d['last_name'] ?? '')) ?: $name,
                    'auth_avatar' => $avatar,
                ]);
                $name = session('auth_name');
            }
        } catch (\Throwable $e) {}

        return view('profile/index', [
            'title'  => 'Akun',
            'name'   => $name,
            'email'  => $email,
            'avatar' => $avatar,
            'first'  => $first,
            'last'   => $last,
            'msg'    => session()->getFlashdata('msg'),
            'errs'   => session()->getFlashdata('errs') ?? [],
        ]);
    }

    public function update()
    {
        if (! session('auth')) return redirect()->to('/login');

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errs', $this->validator->getErrors());
        }

        $token = session('auth_token');
        $first = trim((string) $this->request->getPost('first_name'));
        $last  = trim((string) $this->request->getPost('last_name'));

        try {
            $api = new NutechAPI();
            $response = $api->updateProfile($token, ['first_name' => $first, 'last_name' => $last]);
            $ok  = (($response['status'] ?? 0) >= 200 && ($response['status'] ?? 0) < 300) && (($response['body']['status'] ?? 1) === 0);

            if ($ok) {
                session()->set(['auth_name' => trim($first.' '.$last)]);
                return redirect()->to('/profile')->with('msg', ['type'=>'success','text'=>'Profil berhasil diperbarui.']);
            }

            $msg = $response['body']['message'] ?? 'Gagal memperbarui profil.';
            return redirect()->back()->withInput()->with('msg', ['type'=>'danger','text'=>$msg]);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('msg', ['type'=>'danger','text'=>'Gagal menghubungi server.']);
        }
    }

    public function uploadImage()
    {
        if (! session('auth')) return redirect()->to('/login');

        $file = $this->request->getFile('photo');
        if (! $file || ! $file->isValid()) {
            return redirect()->back()->with('msg', ['type'=>'danger','text'=>'File tidak valid.']);
        }
        if ($file->getSize() > 100 * 1024) {
            return redirect()->back()->with('msg', ['type'=>'danger','text'=>'Ukuran maksimum 100 KB.']);
        }
        if (! in_array($file->getMimeType(), ['image/jpeg','image/png'], true)) {
            return redirect()->back()->with('msg', ['type'=>'danger','text'=>'Format harus JPEG atau PNG.']);
        }
        try {
            $api = new NutechAPI();
            $response = $api->updateProfileImage(session('auth_token'), $file);

            $ok = (($response['status'] ?? 0) >= 200 && ($response['status'] ?? 0) < 300) && (($response['body']['status'] ?? 1) === 0);
            if ($ok) {
                $img = $response['body']['data']['profile_image'] ?? null;
                if ($img) session()->set(['auth_avatar' => $img]);
                return redirect()->to('/profile')->with('msg', ['type'=>'success','text'=>$response['body']['message'] ?? 'Foto profil berhasil diubah.']);
            }
            $msg = $response['body']['message'] ?? 'Gagal mengunggah gambar.';
            return redirect()->back()->with('msg', ['type'=>'danger','text'=>$msg]);

        } catch (\Throwable $e) {
            return redirect()->back()->with('msg', ['type'=>'danger','text'=>'Gagal menghubungi server: '.$e->getMessage()]);
        }
    }
}
