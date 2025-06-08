<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use App\Http\Requests\StoreRombonganRequest;
use App\Http\Requests\UpdateRombonganRequest;
use App\Models\Invoice;
use App\Models\Rombongan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Request as FacadesRequest;
use App\Exports\DataExport;
use Maatwebsite\Excel\Facades\Excel;

class RombonganController extends Controller
{

    public function fo()
    {
        $rombongan = Rombongan::orderBy('created_at', 'desc')->get();
        // Filter dalam collection untuk mendapatkan item pertama yang bukan dari hari ini
        $status = $rombongan->first(function ($item) {
            return $item->created_at->toDateString() !== Carbon::today()->toDateString();
        });
        return view('karyawan.fo.index', compact('rombongan', 'status'));
    }
    public function getRombonganData()
    {
        $rombongan = Rombongan::orderBy('created_at', 'desc')->get();

        return response()->json($rombongan);
    }
    public function getRombonganDatatransaksi()
    {
        $rombongan = Rombongan::where('status', 'datang')->orwhere('status', 'transaksi')->orderBy('created_at', 'desc')->get();

        return response()->json($rombongan);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kode' => 'required|unique:rombongans,kode', // Pastikan kode unik
            'nama' => 'required|string|max:255',
        ]);

        // Menyimpan data ke database
        Rombongan::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'status' => 'datang', // Bisa disesuaikan dengan kebutuhan
            'waktu_datang' => now()->format('H:i')
        ]);

        // Redirect atau tampilkan pesan sukses
        return redirect()->route('fo')->with('success', 'Rombongan berhasil ditambahkan!');
    }

   
public function deleteAll()
{
    // Nonaktifkan pemeriksaan foreign key sementara
    Schema::disableForeignKeyConstraints();

    // Hapus semua data di kedua tabel
    Invoice::truncate();
    Rombongan::truncate();

    // Aktifkan kembali pemeriksaan foreign key
    Schema::enableForeignKeyConstraints();

    // Redirect atau berikan respon setelah penghapusan
    return redirect()->back()->with('success', 'Semua data di tabel Invoices dan Rombongan berhasil dihapus.');
}


    //kasir
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
        $rombongans = Rombongan::where('status', 'transaksi')->orwhere('status', 'datang')->orderBy('created_at', 'desc')->get();
        $invoice = Invoice::where('rombongan_id', $id)->orwhere('user_id', Auth::user()->id)->get();

        return view('karyawan.kasir.cal', compact('rombongan', 'rombongans', 'id', 'invoice'));
    }
    public function getRombonganDetail(Request $request)
    {
        $rombongan = Rombongan::find($request->id);
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
        $rombongans = Rombongan::where('status', 'transaksi')->orwhere('status', 'datang')->orderBy('created_at', 'desc')->get();
        return response()->json($rombongans);
    }

    public function tambahInvoice(Request $request)
    {
        // Validasi data yang diterima
        $request->validate([
            'rombongan_id' => 'required|integer',
            'belanja' => 'required|numeric'
        ]);
    
        try {
            // Dapatkan rombongan
            $rombongan = Rombongan::find($request->rombongan_id);
            
            if ($rombongan && $rombongan->status === 'selesai') {
                // Jika status sudah selesai, kirim respon dengan status selesai
                return response()->json(['status' => 'selesai']);
            }
    
            // Membuat data invoice baru
            $invoice = new Invoice();
            $invoice->user_id = Auth::user()->id;
            $invoice->rombongan_id = $request->rombongan_id;
            $invoice->belanja = $request->belanja;
            $invoice->save();
    
            // Update status rombongan jika belum 'transaksi'
            if ($rombongan && $rombongan->status !== 'transaksi') {
                $rombongan->status = 'transaksi';
                $rombongan->save();
            }
    
            return response()->json(['message' => 'Invoice berhasil ditambahkan']);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }
    


    //BO
    public function bo()
    {
        $rombongan = Rombongan::orderBy('created_at', 'desc')->get();
        // Filter dalam collection untuk mendapatkan item pertama yang bukan dari hari ini
        $status = $rombongan->first(function ($item) {
            return $item->created_at->toDateString() !== Carbon::today()->toDateString();
        });
        return view('karyawan.bo.index', compact('rombongan', 'status'));
    }
    public function detail_transaksi(Request $request)
    {
        $id = $request->id;
        $rombongan = Rombongan::find($id);

        $invoices = Invoice::where('rombongan_id', $id)->with('user')
            ->get()
            ->groupBy('user_id'); // Mengelompokkan berdasarkan user_id
        $userTotals = $invoices->where('rombongan_id', $id)->groupBy('user_id')->map(function ($rows) {
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
        $rombonganId = $request->rombongan_id;  // Get rombongan_id from the AJAX request

        // Fetch the invoices and group by user_id
        $invoices = Invoice::where('rombongan_id', $rombonganId)
            ->with('user')
            ->get()
            ->groupBy('user_id'); // Mengelompokkan berdasarkan user_id

        // Prepare the user totals
        $userTotals = $invoices->map(function ($rows) {
            return [
                'user' => $rows->first()->user,
                'belanja' => $rows->pluck('belanja'),
                'total' => $rows->sum('belanja'),
                'rombongan' => $rows->first()->rombongan
            ];
        });

        // Return the data as JSON
        return response()->json(['userTotals' => $userTotals]);
    }


    public function update_status_transaksi(Request $request)
    {
        // Retrieve the rombongan_id from the request
        $rombonganId = $request->rombongan_id;

        // Find the rombongan record by its ID
        $rombongan = Rombongan::findOrFail($rombonganId);

        // Update the necessary fields
        $rombongan->waktu_pulang = now()->format('H:i');
        $rombongan->status = 'selesai';
        $rombongan->total_belanja = $request->total;
        $rombongan->total_belanja2 = $request->total_belanja2;
        $rombongan->fee = $request->total_fee;

        // Save the updated record
        $rombongan->save();

        // Optionally, return a response or redirect
        return redirect()->back()->with('success', 'Transaksi selesai dan status berhasil diupdate.');
    }

    public function export_excel(){
        return Excel::download(new DataExport(), 'Data Export Rombongan '.date('d-m-Y').'.xlsx');
    }

    public function sync_api(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api-open.olsera.co.id/api/open-api/v1/id/report/salesitemsbydate?per_page=100&from=2025-06-4&to=2025-06-4&page=2',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGktb3Blbi5vbHNlcmEuY28uaWRcL2FwaVwvb3Blbi1hcGlcL3YxXC9pZFwvdG9rZW4iLCJpYXQiOjE3NDkzNjQ2NTgsImV4cCI6MTc0OTQ1MTA1OCwibmJmIjoxNzQ5MzY0NjU4LCJqdGkiOiI2ZmQzZjYxYTgwYWUyMTRiNTZkNWRjZWYxNWQyZTQ5ZDY3OGQyNjQ1NTRhMTY0YjU0YWVjZDZmNDg5NzU1NWI0NDg3MWZiYjg0NTk2ZTNkZjE3NDkzNjQ2NTgiLCJzdWIiOjI0MiwicHJ2IjoiNDI1YzY2MmRjMWU4MDlkM2UxOGNhNDU3NjFkMGY2MWQ4MGJmNmNlMyJ9.klOwVci782sBe7_-mPo-uhb40DA0Ud7tF_Gamq47i7Y'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        dd($response);
        $all = Rombongan::get();
        foreach ($all as $value) {
            if($value->nama == $response->customer_name){
                Rombongan::create([
                    'kode' => $response->customer_name,
                    'nama' => 1,
                    'status' => 'transaksi'
                ]);
            }
        }

        return redirect()->back()->with('success', 'Berhasil sinkronisasi data POS');

    }
}
