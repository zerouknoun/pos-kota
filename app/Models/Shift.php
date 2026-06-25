<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    // Kolom yang diizinkan untuk diisi
    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'initial_cup',
        'final_cup',
        'total_cash',
        'total_qris',
        'total_kasbon',
        'total_revenue',
    ];

    /**
     * Relasi ke tabel Users (Untuk memanggil nama kasir)
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke tabel Orders pada shift ini.
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}