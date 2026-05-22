<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class McuPackageItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'mcu_package_id', 'item_type', 'item_id'
    ];

    public function item()
    {
        return $this->morphTo();
    }
}
