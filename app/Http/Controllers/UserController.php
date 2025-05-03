<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cabang;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{



    // Proses login
    public function index(Request $request)
    {
        // Cek apakah pengguna sudah login
        if (Auth::check()) {
            $user = Auth::user();

            // Redirect berdasarkan role pengguna
            if ($user->role == 'fo') {
                return redirect()->route('fo');
            } elseif ($user->role == 'bo') {
                return redirect()->route('bo');
            } elseif ($user->role == 'kasir') {
                return redirect()->route('kasir');
            } elseif ($user->role == 'master') {
                return redirect()->route('master');
            }
        }

        // Jika belum login, tampilkan halaman login
        return view('login.index');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'pin' => 'required|numeric',
        ]);

        $user = User::where('pin', $request->pin)->first();

        if ($user) {
            if ($user->session_id && $user->session_id !== Session::getId()) {
                return redirect()->back()->with('status', 'Akun ini sudah login di perangkat lain.');
            }

            Auth::login($user);
            $user->session_id = Session::getId(); // Set session ID
            $user->save();

            if ($user->role == 'fo') {
                return redirect()->route('fo');
            } elseif ($user->role == 'bo') {
                return redirect()->route('bo');
            } elseif ($user->role == 'kasir') {
                return redirect()->route('kasir');
            } elseif ($user->role == 'master') {
                return redirect()->route('master');
            }
        }

        return redirect()->back()->with('status', 'Pin Salah');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
    
        // Periksa apakah $user adalah objek valid sebelum mengakses metode save
        if ($user instanceof \App\Models\User) {
            $user->session_id = null;
            $user->save(); // Save hanya jika $user adalah objek User yang benar
        }
    
        // Logout pengguna
        Auth::logout();
    
        // Hapus semua data sesi yang ada
        $request->session()->invalidate();
    
        // Regenerate token CSRF untuk keamanan
        $request->session()->regenerateToken();
    
        // Redirect ke halaman login atau halaman utama
        return redirect('/')->with('status', 'Anda telah berhasil logout');
    }
    

    public function akun()
    {
        $users = User::orderBy('created_at', 'desc')->with('cabang')->where('role','!=','master')->get();
        $cabang = Cabang::all();

        return view('karyawan.bo.akun', compact('users','cabang'));
    }

    public function akun_master()
    {
        $users = User::orderBy('created_at', 'desc')->with('cabang')->get();
        $cabang = Cabang::all();

        return view('karyawan.master.akun', compact('users','cabang'));
    }

    public function master()
    {
        $users = User::orderBy('created_at', 'desc')->with('cabang')->get();
        $cabang = Cabang::all();

        return view('karyawan.master.akun', compact('users','cabang'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'role' => 'required|in:fo,kasir,bo',
            'pin' => 'required|numeric|digits:4|unique:users,pin', // Pastikan PIN unik
        ], [
            'pin.unique' => 'PIN ini sudah digunakan. Harap pilih PIN lain.',
        ]);

        // Tambahkan data user ke database
        $user = new User();
        $user->nama = $request->nama;
        $user->role = $request->role;
        $user->cabang_id = $request->cabang_id;
        $user->pin = $request->pin; // Simpan PIN (lebih baik di-hash jika ini adalah informasi sensitif)

        $user->save();

        // Redirect atau respon dengan pesan berhasil
        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }


    public function destroy(Request $request)
    {
        $userId = $request->id;

        // Cari user berdasarkan id
        $user = User::find($userId);

        if ($user) {
            // Hapus user jika ditemukan
            $user->delete();
            return redirect()->back()->with('success', 'User berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }
    }
    
    

    public function reset()
    {
        $users = User::all();
        
        return view('login.reset', compact('users'));
    }

    public function reset_login(Request $request)
    {
        $user = User::find($request->id);

        if ($user) {
            // Hapus session_id
            $user->session_id = null;
            $user->save();

            // Redirect dengan pesan sukses
            return redirect()->back()->with('success', 'Session berhasil direset.');
        }
    }
}
