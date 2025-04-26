<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CabangController extends Controller
{

    public function cabang()
    {
        $cabang = Cabang::orderBy('created_at', 'desc')->get();

        return view('karyawan.bo.cabang',compact('cabang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        $user = new Cabang();
        $user->nama = $request->nama;
        $user->save();

        return redirect()->back()->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function destroy(Request $request)
    {
        $userId = $request->id;
        $user = Cabang::find($userId);

        if ($user) {
            $user->delete();
            return redirect()->back()->with('success', 'Cabang berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Cabang tidak ditemukan.');
        }
    }
    
}
