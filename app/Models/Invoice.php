<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];
    // Di model Invoice.php
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
