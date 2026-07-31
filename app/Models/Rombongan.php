<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Rombongan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function invoice()
    {
        return $this->hasOne(Invoice::class)->with('user');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    /**
     * Batasi data ke cabang milik user yang sedang login.
     * Setiap user terkunci di cabangnya masing-masing.
     */
    public function scopeCabangUser(Builder $query, ?int $cabangId = null): Builder
    {
        return $query->where('cabang_id', $cabangId ?? Auth::user()?->cabang_id);
    }
}
