<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporans';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'location',
        'date',
        'time',
        'status',
        'presence',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
