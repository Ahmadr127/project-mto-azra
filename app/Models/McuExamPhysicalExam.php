<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McuExamPhysicalExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'mcu_registration_id', 'mcu_physical_exam_id', 'result_value', 'status'
    ];

    public function registration()
    {
        return $this->belongsTo(McuRegistration::class, 'mcu_registration_id');
    }

    public function physicalExam()
    {
        return $this->belongsTo(McuPhysicalExam::class, 'mcu_physical_exam_id');
    }
}