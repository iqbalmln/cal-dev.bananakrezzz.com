<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Services\OlseraService;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CabangController extends Controller
{

    public function cabang()
    {
        $cabang = Cabang::orderBy('created_at', 'desc')->get();

        return view('karyawan.master.cabang',compact('cabang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'olsera_app_id' => 'nullable|string|max:255',
            'olsera_secret_key' => 'nullable|string|max:255',
        ]);

        $cabang = new Cabang();
        $cabang->nama = $request->nama;
        $cabang->olsera_app_id = $request->olsera_app_id;
        $cabang->olsera_secret_key = $request->olsera_secret_key;
        $cabang->sync_aktif = $request->boolean('sync_aktif');
        $cabang->save();

        return redirect()->back()->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:cabangs,id',
            'nama' => 'required|string|max:255',
            'olsera_app_id' => 'nullable|string|max:255',
            'olsera_secret_key' => 'nullable|string|max:255',
        ]);

        $cabang = Cabang::findOrFail($request->id);

        $cabang->nama = $request->nama;
        $cabang->olsera_app_id = $request->olsera_app_id;
        $cabang->sync_aktif = $request->boolean('sync_aktif');

        // Secret key dikosongkan di form = tidak diubah, supaya tidak perlu
        // menampilkan ulang nilai lamanya di halaman.
        if (filled($request->olsera_secret_key)) {
            $cabang->olsera_secret_key = $request->olsera_secret_key;
        }

        $cabang->save();

        // Kredensial berubah -> token lama tidak valid lagi.
        OlseraService::lupakanTokenCabang($cabang);

        return redirect()->back()->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $userId = $request->id;
        $user = Cabang::find($userId);

        if ($user) {
            OlseraService::lupakanTokenCabang($user);
            $user->delete();
            return redirect()->back()->with('success', 'Cabang berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Cabang tidak ditemukan.');
        }
    }

}
