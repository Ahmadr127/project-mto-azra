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

    public function getResultAttribute($value)
    {
        $data = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data['findings'] ?? '';
        }
        return $value;
    }

    public function getIsNormalAttribute()
    {
        $value = $this->attributes['result'] ?? '';
        $data = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data['is_normal'] ?? true;
        }
        if (!$value) return true;
        $vLower = strtolower(trim($value));
        return $vLower === 'normal' || $vLower === 'dalam batas normal' || $vLower === '-';
    }

    public function getFindingsAttribute()
    {
        $value = $this->attributes['result'] ?? '';
        $data = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data['findings'] ?? '';
        }
        return $value ?: '';
    }
}