<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McuExamRadiology extends Model
{
    use HasFactory;

    protected $fillable = [
        'mcu_registration_id', 'mcu_radiology_id', 'result', 'status'
    ];

    public function registration()
    {
        return $this->belongsTo(McuRegistration::class, 'mcu_registration_id');
    }

    public function radiology()
    {
        return $this->belongsTo(McuRadiology::class, 'mcu_radiology_id');
    }
}