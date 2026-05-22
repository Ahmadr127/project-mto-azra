<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McuExamAnamnesis extends Model
{
    use HasFactory;

    protected $table = 'mcu_exam_anamneses';

    protected $fillable = [
        'mcu_registration_id', 'mcu_anamnesis_id', 'result', 'status'
    ];

    public function registration()
    {
        return $this->belongsTo(McuRegistration::class, 'mcu_registration_id');
    }

    public function anamnesis()
    {
        return $this->belongsTo(McuAnamnesis::class, 'mcu_anamnesis_id');
    }
}