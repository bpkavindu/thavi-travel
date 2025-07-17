<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoSpot extends Model
{
    use HasFactory;

        protected $fillable = [
        'name',
        'location',
        'description',
        'image_path',
        'qr_code',
    ];
}
