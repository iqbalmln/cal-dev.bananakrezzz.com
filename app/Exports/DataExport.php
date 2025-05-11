<?php

namespace App\Exports;

use App\Models\Rombongan;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class DataExport implements FromView
{
    public function __construct()
    {
    }

    public function view(): View
    {
        $query = Rombongan::with('invoice')->where('status','selesai')->get();
        return view('export.rombongan', [
            'data' => $query
        ]);
    }

    public function columnFormats(): array
    {
        return [];
    }
}
