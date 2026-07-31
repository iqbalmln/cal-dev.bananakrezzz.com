<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $hidden = ['olsera_secret_key'];

    protected $casts = [
        'olsera_secret_key' => 'encrypted',
        'sync_aktif' => 'boolean',
        'last_sync' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function rombongans()
    {
        return $this->hasMany(Rombongan::class);
    }

    /**
     * Cabang yang kredensial Olsera-nya lengkap dan sync-nya dinyalakan.
     */
    public function scopeSiapSync(Builder $query): Builder
    {
        return $query->where('sync_aktif', true)
            ->whereNotNull('olsera_app_id')
            ->whereNotNull('olsera_secret_key');
    }

    /**
     * Versi non-query dari scopeSiapSync(). Namanya sengaja dibedakan supaya
     * tidak menutupi pemanggilan scope Cabang::siapSync().
     */
    public function bolehSync(): bool
    {
        return $this->sync_aktif
            && filled($this->olsera_app_id)
            && filled($this->olsera_secret_key);
    }
}
