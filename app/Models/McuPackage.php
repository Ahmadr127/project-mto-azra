<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McuPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'base_price', 'description', 'display_order', 'status'
    ];

    public function items()
    {
        return $this->hasMany(McuPackageItem::class);
    }
}
