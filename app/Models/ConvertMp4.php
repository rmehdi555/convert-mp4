<?php

namespace App\Models;

use App\Enums\ConvertStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConvertMp4 extends Model
{
    use HasFactory;

    protected $table = 'convert_mp4';

    protected $fillable = [
        'path_mp4',
        'path_m3u8',
        'status',
        'error_message',
    ];

    protected $casts = [
        'status' => ConvertStatus::class,
    ];

    protected $attributes = [
        'status' => 'pending',
    ];
}
