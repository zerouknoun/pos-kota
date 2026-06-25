<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * Kolom yang diizinkan untuk diisi (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'price',
    ];
}