<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{Auth, CSRF, DB, Request, Response, Session, Validator, View};

class ProfilController
{
    public function index(): void
    {
        View::render('admin.profil', ['title' => 'Profil Admin', 'user' => Auth::user()], 'admin');
    }

    public function update(): void
    {
        CSRF::check();
        $user = Auth::user();
        $data = Request::only(['nama_lengkap', 'username', 'password']);
        
        $v = Validator::make($data, ['nama_lengkap' => 'required|max:150', 'username' => 'required|max:100']);
        if (!$v->validate()) {
            Session::toast('error', $v->firstError());
            Response::redirect(url('admin/profil'));
        }

        // Cek username unik
        if (DB::table('users')->where('school_id', current_school_id())->where('username', $data['username'])->where('id', '!=', $user['id'])->exists()) {
            Session::toast('error', 'Username sudah digunakan.');
            Response::redirect(url('admin/profil'));
        }

        $updateData = ['nama_lengkap' => $data['nama_lengkap'], 'username' => $data['username']];
        if (!empty($data['password'])) {
            $updateData['password_hash'] = Auth::hashPassword($data['password']);
        }

        DB::table('users')->where('id', $user['id'])->update($updateData);
        Session::set('nama_lengkap', $data['nama_lengkap']); // Update session name
        Session::toast('success', 'Profil berhasil diperbarui.');
        Response::redirect(url('admin/profil'));
    }
}