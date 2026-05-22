<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McuRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'mcu_package_id', 'registration_date', 'status',
        'conclusion', 'recommendation', 'resume_by', 'resume_date'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function resumeDoctor()
    {
        return $this->belongsTo(User::class, 'resume_by');
    }

    public function package()
    {
        return $this->belongsTo(McuPackage::class, 'mcu_package_id');
    }

    public function examMedicalActions()
    {
        return $this->hasMany(McuExamMedicalAction::class);
    }

    public function examLabs()
    {
        return $this->hasMany(McuExamLab::class);
    }

    public function examRadiologies()
    {
        return $this->hasMany(McuExamRadiology::class);
    }

    public function examAnamneses()
    {
        return $this->hasMany(McuExamAnamnesis::class);
    }

    public function physicalExamResult()
    {
        return $this->hasOne(McuPhysicalExamResult::class);
    }
}