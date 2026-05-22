<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McuRadiology extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'category', 'price', 'description', 'display_order', 'status'
    ];
}
