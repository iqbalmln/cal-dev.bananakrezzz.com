<?php

namespace App\Exports;

use App\Models\Rombongan;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class DataExport implements FromView
{
    public function __construct(protected ?int $cabangId = null)
    {
    }

    public function view(): View
    {
        $query = Rombongan::with('invoice')
            ->where('status', 'selesai')
            ->when($this->cabangId, fn($q) => $q->where('cabang_id', $this->cabangId))
            ->get();

        return view('export.rombongan', [
            'data' => $query
        ]);
    }

    public function columnFormats(): array
    {
        return [];
    }
}
