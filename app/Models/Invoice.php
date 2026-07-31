<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Di model Invoice.php
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->with('cabang');
    }

    public function rombongan()
    {
        return $this->belongsTo(Rombongan::class);
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function scopeCabangUser(Builder $query, ?int $cabangId = null): Builder
    {
        return $query->where('cabang_id', $cabangId ?? Auth::user()?->cabang_id);
    }
}
