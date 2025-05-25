<?php

namespace App\Http\Controllers;

use App\Models\Rombongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{

    public function rombongan($id)
    {
        $data = Rombongan::where('id', $id)->first();
        echo json_encode($data);
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
