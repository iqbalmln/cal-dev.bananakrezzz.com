<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use App\Models\Invoice;
use App\Models\Rombongan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DataExport;


class RombonganController extends Controller
{
    // ==========================
    // FRONT OFFICE
    // ==========================
    public function fo()
    {
        $rombongan = Rombongan::orderBy('created_at', 'desc')->get();
        $status = $rombongan->first(function ($item) {
            return $item->created_at->toDateString() !== Carbon::today()->toDateString();
        });

        return view('karyawan.fo.index', compact('rombongan', 'status'));
    }

    public function getRombonganData()
    {
        $rombongan = Rombongan::whereDate('created_at', date("Y-m-d"))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($rombongan);
    }

    public function getRombonganDatatransaksi()
    {
        $rombongan = Rombongan::where(function ($q) {
                $q->where('status', 'datang')
                  ->orWhere('status', 'transaksi');
            })
            ->whereDate('created_at', date("Y-m-d"))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($rombongan);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:rombongans,kode',
            'nama' => 'required|string|max:255',
        ]);

        Rombongan::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'status' => 'datang',
            'waktu_datang' => now()->format('H:i')
        ]);

        return redirect()->route('fo')->with('success', 'Rombongan berhasil ditambahkan!');
    }

    public function deleteAll()
    {
        Schema::disableForeignKeyConstraints();
        Invoice::truncate();
        Rombongan::truncate();
        Schema::enableForeignKeyConstraints();

        return redirect()->back()->with('success', 'Semua data berhasil dihapus.');
    }

    // ==========================
    // KASIR
    // ==========================
    public function kasir()
    {
        $rombongan = Rombongan::orderBy('created_at', 'desc')->get();
        $status = $rombongan->first(function ($item) {
            return $item->created_at->toDateString() !== Carbon::today()->toDateString();
        });

        return view('karyawan.kasir.index', compact('rombongan', 'status'));
    }

    public function cal(Request $request)
    {
        $id = $request->id;
        $rombongan = Rombongan::find($id);

        if (!$rombongan) {
            return redirect()->back()->with('error', 'Rombongan tidak ditemukan.');
        }

        $rombongans = Rombongan::whereIn('status', ['transaksi', 'datang'])
            ->orderBy('created_at', 'desc')
            ->get();

        $invoice = Invoice::where('rombongan_id', $id)
            ->orWhere('user_id', Auth::user()->id)
            ->get();

        return view('karyawan.kasir.cal', compact('rombongan', 'rombongans', 'id', 'invoice'));
    }

    public function getRombonganDetail(Request $request)
    {
        $rombongan = Rombongan::find($request->id);
        if (!$rombongan) {
            return response()->json(['error' => 'Rombongan tidak ditemukan'], 404);
        }

        $invoice = Invoice::where('rombongan_id', $rombongan->id)
            ->where('user_id', Auth::user()->id)
            ->get();

        return response()->json([
            'rombongan' => $rombongan,
            'invoice' => $invoice
        ]);
    }

    public function getRombongansData()
    {
        $rombongans = Rombongan::whereIn('status', ['transaksi', 'datang'])
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

            if (!$rombongan) {
                return response()->json(['message' => 'Rombongan tidak ditemukan'], 404);
            }

            if ($rombongan->status === 'selesai') {
                return response()->json(['status' => 'selesai']);
            }

            $invoice = new Invoice();
            $invoice->user_id = Auth::user()->id;
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
        $rombongan = Rombongan::orderBy('created_at', 'desc')->get();
        $status = $rombongan->first(function ($item) {
            return $item->created_at->toDateString() !== Carbon::today()->toDateString();
        });

        return view('karyawan.bo.index', compact('rombongan', 'status'));
    }

    public function detail_transaksi(Request $request)
    {
        $id = $request->id;
        $rombongan = Rombongan::find($id);

        if (!$rombongan) {
            return redirect()->back()->with('error', 'Rombongan tidak ditemukan.');
        }

        $invoices = Invoice::where('rombongan_id', $id)->get();

        $total_add = $invoices->sum('belanja');
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
        $rombonganId = $request->rombongan_id;
        $rombongan = Rombongan::find($rombonganId);

        if (!$rombongan) {
            return response()->json(['error' => 'Rombongan tidak ditemukan'], 404);
        }

        $invoices = Invoice::where('rombongan_id', $rombonganId)
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

        if (!$rombongan) {
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
        return Excel::download(new DataExport(), 'Data Export Rombongan ' . date('d-m-Y') . '.xlsx');
    }

    // ==========================
    // SYNC API (BUTTON & CRON)
    // ==========================
    public function sync_api(Request $request)
    {
        if (empty($request->result)) {
            return response()->json(['status' => 'success']);
        }

        $userId = Auth::check() ? Auth::id() : 1; // fallback untuk cron

        foreach ($request->result as $value) {
            $check_invoice = Invoice::where('id_pos', $value['id_pos'])->first();

            if ($check_invoice) continue;

            $existing = Rombongan::where('nama', $value['name'])
                ->whereDate('created_at', $value['order_date'])
                ->first();

            if ($existing) {
                $existing->increment('total_belanja', $value['price']);

                Invoice::create([
                    'user_id' => $userId,
                    'rombongan_id' => $existing->id,
                    'belanja' => $value['price'],
                    'id_pos' => $value['id_pos'],
                ]);
            } else {
                $rombongan = Rombongan::create([
                    'nama' => $value['name'],
                    'kode' => 1,
                    'total_belanja' => $value['price'],
                    'status' => 'transaksi',
                    'waktu_datang' => $value['order_time'],
                    'created_at' => $value['order_date']
                ]);

                Invoice::create([
                    'user_id' => $userId,
                    'rombongan_id' => $rombongan->id,
                    'belanja' => $value['price'],
                    'id_pos' => $value['id_pos'] ?? null,
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function updateNamaDisplay(Request $request)
    {
        $request->validate([
            'rombongan_id' => 'required|integer',
            'nama_display' => 'required|string|max:255',
        ]);

        $rombongan = Rombongan::find($request->rombongan_id);

        if (!$rombongan) {
            return response()->json(['message' => 'Rombongan tidak ditemukan'], 404);
        }

        $rombongan->update(['nama_display' => $request->nama_display]);

        return response()->json(['message' => 'Nama berhasil diperbarui', 'nama_display' => $rombongan->nama_display]);
    }

    public function sync_api_success()
    {
        DB::table('rombongans')->update(['last_sync' => now()]);
        return redirect()->back()->with('success', 'Sinkronisasi Data Berhasil');
    }
}
