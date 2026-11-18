<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\CSRF;
use App\Core\DB;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;

class SlotJamController
{
    public function index(): void
    {
        // GANTI $data MENJADI $slots
        $slots = DB::raw("SELECT * FROM slot_jam WHERE school_id = ? ORDER BY jam_ke ASC", [current_school_id()]);

        View::render('admin.slot_jam.index', [
            'title' => 'Slot Jam Pelajaran',
            'slots' => $slots // <--- Kirim sebagai $slots
        ], 'admin');
    }
    public function store(): void
    {
        CSRF::check();
        $data = Request::only(['jam_ke', 'jam_mulai', 'jam_selesai']);
        $data['school_id'] = current_school_id();

        $v = Validator::make($data, [
            'jam_ke' => 'required|numeric',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required'
        ]);

        if (!$v->validate()) {
            Session::flash('error', $v->firstError());
            Response::redirect(url('admin/slot-jam'));
        }

        if (strtotime($data['jam_mulai']) >= strtotime($data['jam_selesai'])) {
            Session::flash('error', 'Jam selesai harus lebih besar dari jam mulai.');
            Response::redirect(url('admin/slot-jam'));
        }

        if (DB::table('slot_jam')->where('school_id', current_school_id())->where('jam_ke', $data['jam_ke'])->exists()) {
            Session::flash('error', 'Jam ke-' . $data['jam_ke'] . ' sudah ada.');
            Response::redirect(url('admin/slot-jam'));
        }

        DB::table('slot_jam')->insert($data);
        Session::toast('success', 'Slot jam berhasil ditambahkan.');
        Response::redirect(url('admin/slot-jam'));
    }

    public function update(int $id): void
    {
        CSRF::check();
        $data = Request::only(['jam_ke', 'jam_mulai', 'jam_selesai']);

        $v = Validator::make($data, [
            'jam_ke' => 'required|numeric',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required'
        ]);

        if (!$v->validate()) {
            Session::flash('error', $v->firstError());
            Response::redirect(url('admin/slot-jam'));
        }

        if (strtotime($data['jam_mulai']) >= strtotime($data['jam_selesai'])) {
            Session::flash('error', 'Jam selesai harus lebih besar dari jam mulai.');
            Response::redirect(url('admin/slot-jam'));
        }

        if (DB::table('slot_jam')->where('school_id', current_school_id())->where('jam_ke', $data['jam_ke'])->where('id', '!=', $id)->exists()) {
            Session::toast('error', 'Jam ke-' . $data['jam_ke'] . ' sudah ada.');
            Response::redirect(url('admin/slot-jam'));
        }

        DB::table('slot_jam')->where('id', $id)->where('school_id', current_school_id())->update($data);
        Session::toast('success', 'Slot jam berhasil diperbarui.');
        Response::redirect(url('admin/slot-jam'));
    }

    public function destroy(int $id): void
    {
        CSRF::check();
        
        // Cek apakah dipakai di jadwal
        $dipakai = DB::table('jadwal')->where('slot_jam_id', $id)->exists();
        if ($dipakai) {
            Session::toast('error', 'Tidak bisa dihapus karena slot jam ini sudah dipakai di jadwal pelajaran.');
            Response::redirect(url('admin/slot-jam'));
        }

        DB::table('slot_jam')->where('id', $id)->where('school_id', current_school_id())->delete();
        Session::toast('success', 'Slot jam berhasil dihapus.');
        Response::redirect(url('admin/slot-jam'));
    }
}