<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McuPhysicalExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'mcu_registration_id', 'doctor_id', 'vital_signs', 'head_and_neck',
        'thorax', 'abdomen', 'urogenital', 'extremities', 'others', 'status'
    ];

    protected $casts = [
        'vital_signs' => 'array',
        'head_and_neck' => 'array',
        'thorax' => 'array',
        'abdomen' => 'array',
        'urogenital' => 'array',
        'extremities' => 'array',
        'others' => 'array',
    ];

    public function registration()
    {
        return $this->belongsTo(McuRegistration::class, 'mcu_registration_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}