<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McuExamMedicalAction extends Model
{
    use HasFactory;
    
    protected $table = 'mcu_exam_medical_actions';

    protected $fillable = [
        'mcu_registration_id', 'mcu_medical_action_id', 'result', 'doctor_id', 'status'
    ];

    public function registration()
    {
        return $this->belongsTo(McuRegistration::class, 'mcu_registration_id');
    }

    public function medicalAction()
    {
        return $this->belongsTo(McuMedicalAction::class, 'mcu_medical_action_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}