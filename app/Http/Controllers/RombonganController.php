<?php

namespace App\Http\Controllers;

use App\Jobs\SyncOlseraJob;
use App\Models\Invoice;
use App\Models\Rombongan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DataExport;


class RombonganController extends Controller
{
    /**
     * Cabang milik user yang sedang login. Setiap user terkunci di satu cabang.
     */
    protected function cabangId(): ?int
    {
        return Auth::user()?->cabang_id;
    }

    /**
     * Pastikan sebuah rombongan/invoice memang milik cabang user.
     */
    protected function milikCabang($model): bool
    {
        return $model && (int) $model->cabang_id === (int) $this->cabangId();
    }

    // ==========================
    // FRONT OFFICE
    // ==========================
    public function fo()
    {
        $rombongan = Rombongan::cabangUser()->orderBy('created_at', 'desc')->get();
        $status = $rombongan->first(function ($item) {
            return $item->created_at->toDateString() !== Carbon::today()->toDateString();
        });

        return view('karyawan.fo.index', compact('rombongan', 'status'));
    }

    public function getRombonganData()
    {
        $rombongan = Rombongan::cabangUser()
            ->whereDate('created_at', date("Y-m-d"))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($rombongan);
    }

    public function getRombonganDatatransaksi()
    {
        $rombongan = Rombongan::cabangUser()
            ->whereIn('status', ['datang', 'transaksi'])
            ->whereDate('created_at', date("Y-m-d"))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($rombongan);
    }

    public function store(Request $request)
    {
        $cabangId = $this->cabangId();

        $request->validate([
            // Kode cukup unik per cabang, bukan unik global.
            'kode' => [
                'required',
                Rule::unique('rombongans', 'kode')->where(fn($q) => $q->where('cabang_id', $cabangId)),
            ],
            'nama' => 'required|string|max:255',
        ]);

        Rombongan::create([
            'cabang_id' => $cabangId,
            'kode' => $request->kode,
            'nama' => $request->nama,
            'status' => 'datang',
            'waktu_datang' => now()->format('H:i')
        ]);

        return redirect()->route('fo')->with('success', 'Rombongan berhasil ditambahkan!');
    }

    public function deleteAll()
    {
        // Hanya hapus data cabang sendiri — jangan sentuh cabang lain.
        $cabangId = $this->cabangId();

        Invoice::where('cabang_id', $cabangId)->delete();
        Rombongan::where('cabang_id', $cabangId)->delete();

        return redirect()->back()->with('success', 'Semua data cabang ini berhasil dihapus.');
    }

    // ==========================
    // KASIR
    // ==========================
    public function kasir()
    {
        $rombongan = Rombongan::cabangUser()->orderBy('created_at', 'desc')->get();
        $status = $rombongan->first(function ($item) {
            return $item->created_at->toDateString() !== Carbon::today()->toDateString();
        });

        return view('karyawan.kasir.index', compact('rombongan', 'status'));
    }

    public function cal(Request $request)
    {
        $id = $request->id;
        $rombongan = Rombongan::find($id);

        if (!$this->milikCabang($rombongan)) {
            return redirect()->back()->with('error', 'Rombongan tidak ditemukan.');
        }

        $rombongans = Rombongan::cabangUser()
            ->whereIn('status', ['transaksi', 'datang'])
            ->orderBy('created_at', 'desc')
            ->get();

        $invoice = Invoice::cabangUser()
            ->where(function ($q) use ($id) {
                $q->where('rombongan_id', $id)
                  ->orWhere('user_id', Auth::id());
            })
            ->get();

        return view('karyawan.kasir.cal', compact('rombongan', 'rombongans', 'id', 'invoice'));
    }

    public function getRombonganDetail(Request $request)
    {
        $rombongan = Rombongan::find($request->id);

        if (!$this->milikCabang($rombongan)) {
            return response()->json(['error' => 'Rombongan tidak ditemukan'], 404);
        }

        $invoice = Invoice::where('rombongan_id', $rombongan->id)
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'rombongan' => $rombongan,
            'invoice' => $invoice
        ]);
    }

    public function getRombongansData()
    {
        $rombongans = Rombongan::cabangUser()
            ->whereIn('status', ['transaksi', 'datang'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($rombongans);
    }

    public function tambahInvoice(Request $request)
    {
        $request->validate([
            'rombongan_id' => 'required|integer',
            'belanja' => 'required|numeric'
        ]);

        try {
            $rombongan = Rombongan::find($request->rombongan_id);

            if (!$this->milikCabang($rombongan)) {
                return response()->json(['message' => 'Rombongan tidak ditemukan'], 404);
            }

            if ($rombongan->status === 'selesai') {
                return response()->json(['status' => 'selesai']);
            }

            $invoice = new Invoice();
            $invoice->cabang_id = $this->cabangId();
            $invoice->user_id = Auth::id();
            $invoice->rombongan_id = $rombongan->id;
            $invoice->belanja = $request->belanja;
            $invoice->save();

            if ($rombongan->status !== 'transaksi') {
                $rombongan->status = 'transaksi';
                $rombongan->save();
            }

            return response()->json(['message' => 'Invoice berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    // ==========================
    // BACK OFFICE
    // ==========================
    public function bo()
    {
        $rombongan = Rombongan::cabangUser()->orderBy('created_at', 'desc')->get();
        $status = $rombongan->first(function ($item) {
            return $item->created_at->toDateString() !== Carbon::today()->toDateString();
        });

        $cabang = Auth::user()?->cabang;

        return view('karyawan.bo.index', compact('rombongan', 'status', 'cabang'));
    }

    public function detail_transaksi(Request $request)
    {
        $id = $request->id;
        $rombongan = Rombongan::find($id);

        if (!$this->milikCabang($rombongan)) {
            return redirect()->back()->with('error', 'Rombongan tidak ditemukan.');
        }

        $total_add = Invoice::where('rombongan_id', $id)->sum('belanja');
        $rombongan->update(['total_belanja' => $total_add]);

        $invoices = Invoice::where('rombongan_id', $id)
            ->with('user')
            ->get()
            ->groupBy('user_id');

        $userTotals = $invoices->map(function ($rows) {
            return [
                'user' => $rows->first()->user,
                'belanja' => $rows->pluck('belanja'),
                'total' => $rows->sum('belanja')
            ];
        });

        $grandTotal = $userTotals->sum('total');
        return view('karyawan.bo.detail', compact('rombongan', 'invoices', 'grandTotal'));
    }

    public function invoiceDetail(Request $request)
    {
        $rombongan = Rombongan::find($request->rombongan_id);

        if (!$this->milikCabang($rombongan)) {
            return response()->json(['error' => 'Rombongan tidak ditemukan'], 404);
        }

        $invoices = Invoice::where('rombongan_id', $rombongan->id)
            ->with('user')
            ->get()
            ->groupBy('user_id');

        $userTotals = $invoices->map(function ($rows) {
            return [
                'user' => $rows->first()->user,
                'belanja' => $rows->pluck('belanja'),
                'total' => $rows->sum('belanja'),
                'rombongan' => $rows->first()->rombongan
            ];
        });

        return response()->json(['userTotals' => $userTotals]);
    }

    public function update_status_transaksi(Request $request)
    {
        $rombongan = Rombongan::find($request->rombongan_id);

        if (!$this->milikCabang($rombongan)) {
            return redirect()->back()->with('error', 'Rombongan tidak ditemukan.');
        }

        $rombongan->update([
            'waktu_pulang' => now()->format('H:i'),
            'status' => 'selesai',
            'total_belanja' => $request->total,
            'total_belanja2' => $request->total_belanja2,
            'fee' => $request->total_fee,
        ]);

        return redirect()->back()->with('success', 'Transaksi selesai dan status berhasil diupdate.');
    }

    public function export_excel()
    {
        $cabang = Auth::user()?->cabang;
        $namaCabang = $cabang?->nama ?: 'Semua';

        return Excel::download(
            new DataExport($this->cabangId()),
            "Data Export Rombongan {$namaCabang} " . date('d-m-Y') . '.xlsx'
        );
    }

    // ==========================
    // SYNC OLSERA (TOMBOL MANUAL)
    // ==========================

    /**
     * Tombol "Sinkronkan Data" hanya menitipkan pekerjaan ke antrian.
     * Kredensial Olsera tidak pernah keluar dari server.
     */
    public function syncManual(Request $request)
    {
        $request->validate([
            'tanggal' => 'nullable|date_format:Y-m-d',
        ]);

        $cabang = Auth::user()?->cabang;

        if (!$cabang) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun Anda belum terhubung ke cabang mana pun.',
            ], 422);
        }

        if (!$cabang->bolehSync()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kredensial Olsera cabang ini belum diisi atau sync sedang dimatikan. Atur di menu Cabang.',
            ], 422);
        }

        $tanggal = $request->input('tanggal') ?: now()->format('Y-m-d');

        SyncOlseraJob::dispatch($cabang, $tanggal, Auth::id());

        return response()->json([
            'status' => 'antri',
            'message' => 'Sinkronisasi sedang diproses di latar belakang.',
            'tanggal' => $tanggal,
        ]);
    }

    /**
     * Dipanggil berkala oleh halaman BO untuk tahu sync sudah selesai atau belum.
     */
    public function syncStatus()
    {
        $cabang = Auth::user()?->cabang;

        return response()->json([
            'last_sync' => $cabang?->last_sync?->toIso8601String(),
            'last_sync_label' => $cabang?->last_sync?->translatedFormat('d F Y, H:i'),
            // Dipakai halaman BO untuk membedakan "masih mengantre" dari
            // "sedang menarik data", supaya pengguna tahu proses tidak macet.
            'antrian' => DB::table('jobs')->count(),
        ]);
    }

    public function updateNamaDisplay(Request $request)
    {
        $request->validate([
            'rombongan_id' => 'required|integer',
            'nama_display' => 'required|string|max:255',
        ]);

        $rombongan = Rombongan::find($request->rombongan_id);

        if (!$this->milikCabang($rombongan)) {
            return response()->json(['message' => 'Rombongan tidak ditemukan'], 404);
        }

        $rombongan->update(['nama_display' => $request->nama_display]);

        return response()->json(['message' => 'Nama berhasil diperbarui', 'nama_display' => $rombongan->nama_display]);
    }
}
