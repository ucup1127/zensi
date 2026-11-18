<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\CSRF;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

class AuthController
{
    public function redirectRoot(): void
    {
        if (Auth::check()) {
            Response::redirect(url('dashboard'));
        }
        Response::redirect(url('login'));
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            Response::redirect(url('dashboard'));
        }
        View::render('auth.login', [
            'error' => Session::flash('error'),
        ], 'auth');
    }

       public function login(): void
    {
        CSRF::check();
        $username = Request::string('username');
        $password = Request::string('password');

        $v = Validator::make(['username' => $username, 'password' => $password], ['username' => 'required|max:100', 'password' => 'required|min:4']);
        if (!$v->validate()) {
            Session::flash('error', $v->firstError());
            Response::redirect(url('login'));
        }

        $schoolId = (int) ($_ENV['DEFAULT_SCHOOL_ID'] ?? 1);

        if (!Auth::attempt($username, $password, $schoolId)) {
            Session::flash('error', 'Username atau password salah.');
            Response::redirect(url('login'));
        }

        if (Session::get('must_change_password')) {
            Response::redirect(url('ganti-password'));
        }

        Response::redirect(url('dashboard'));
    }

    public function logout(): void
    {
        CSRF::check();
        Auth::logout();
        Session::flash('info', 'Anda telah logout.');
        Response::redirect(url('login'));
    }

    public function showChangePassword(): void
    {
        View::render('auth.change-password', [
            'error'   => Session::flash('error'),
            'success' => Session::flash('success'),
            'wajib'   => Session::get('must_change_password'),
        ], 'auth');
    }

    public function changePassword(): void
    {
        CSRF::check();

        $current = Request::string('current_password');
        $new     = Request::string('new_password');
        $confirm = Request::string('confirm_password');

        $v = Validator::make(
            ['current_password' => $current, 'new_password' => $new, 'confirm_password' => $confirm],
            ['current_password' => 'required', 'new_password' => 'required|min:6', 'confirm_password' => 'required|min:6']
        );

        if (!$v->validate()) {
            Session::flash('error', $v->firstError());
            Response::redirect(url('ganti-password'));
        }

        if ($new !== $confirm) {
            Session::flash('error', 'Konfirmasi password tidak cocok.');
            Response::redirect(url('ganti-password'));
        }

        $user = Auth::user();
        if (!$user || !password_verify($current, $user['password_hash'])) {
            Session::flash('error', 'Password saat ini salah.');
            Response::redirect(url('ganti-password'));
        }

        DB::table('users')->where('id', $user['id'])->update([
            'password_hash'        => Auth::hashPassword($new),
            'must_change_password' => 0,
            'updated_at'           => date('Y-m-d H:i:s'),
        ]);

        Session::set('must_change_password', false);
        Session::flash('success', 'Password berhasil diubah.');
        Response::redirect(url('dashboard'));
    }

     public function verifySsoToken(): void
    {
        header('Content-Type: application/json');
        
        $token = $_POST['token'] ?? '';
        if (empty($token)) {
            http_response_code(400);
            echo json_encode(['error' => 'Token required']);
            exit;
        }
        
        try {
            // Cari token yang valid dan belum kedaluwarsa
            $record = DB::rawOne("
                SELECT s.*, u.nis, u.nama_lengkap, u.username, u.role 
                FROM sso_tokens s 
                JOIN users u ON s.user_id = u.id 
                JOIN siswa sw ON sw.user_id = u.id
                WHERE s.token = ? AND s.expires_at > NOW()
            ", [$token]);
            
            if (!$record) {
                http_response_code(401);
                echo json_encode(['error' => 'Token invalid or expired']);
                exit;
            }
            
            // Hapus token setelah dipakai (sekali pakai)
            DB::table('sso_tokens')->where('token', $token)->delete();
            
            echo json_encode([
                'success' => true,
                'user' => [
                    'nisn'     => $record['nis'],
                    'username' => $record['username'],
                    'name'     => $record['nama_lengkap'],
                    'school'   => $record['school_slug'],
                ]
            ]);
            exit;

        } catch (\Exception $e) {
            // Tangkap error database dan kembalikan sebagai JSON (bukan HTML)
            http_response_code(500);
            echo json_encode(['error' => 'Server Error', 'message' => $e->getMessage()]);
            exit;
        }
    }
}