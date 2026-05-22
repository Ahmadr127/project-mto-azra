<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McuExamLab extends Model
{
    use HasFactory;

    protected $fillable = [
        'mcu_registration_id', 'mcu_lab_id', 'result_value', 'normal_value', 'status'
    ];

    public function registration()
    {
        return $this->belongsTo(McuRegistration::class, 'mcu_registration_id');
    }

    public function lab()
    {
        return $this->belongsTo(McuLab::class, 'mcu_lab_id');
    }
}