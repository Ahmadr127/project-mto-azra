<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_code', 'nik', 'name', 'gender', 'birth_date', 'age',
        'address', 'phone', 'company', 'department', 'employee_status', 'bpjs'
    ];

    public function mcuRegistrations()
    {
        return $this->hasMany(McuRegistration::class);
    }
}