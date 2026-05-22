<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class McuMedicalAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'category', 'price', 'description', 'display_order', 'status'
    ];
}
