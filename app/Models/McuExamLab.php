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

    public function getResultAttribute()
    {
        return $this->result_value;
    }

    public function getIsAbnormalAttribute()
    {
        $val = trim($this->result_value);
        if ($val === '' || $val === null || strtolower($val) === 'dalam batas normal' || strtolower($val) === 'normal' || strtolower($val) === '-') {
            return false;
        }

        $norm = trim($this->normal_value);
        if ($norm === '' || $norm === null || $norm === '-') {
            return false;
        }

        // Get patient gender: L (Male) or P (Female)
        $gender = 'L';
        if ($this->registration && $this->registration->patient) {
            $gender = $this->registration->patient->gender; // L or P
        }

        // Parse gender specific first if contains M and F
        $targetNorm = $norm;
        if (preg_match('/M\s*\(([^)]+)\)/i', $norm, $matchM) && preg_match('/F\s*\(([^)]+)\)/i', $norm, $matchF)) {
            if ($gender === 'L') {
                $targetNorm = $matchM[1]; // e.g. "14.0-18.0" or "<=50"
            } else {
                $targetNorm = $matchF[1]; // e.g. "12.0-16.0" or "<=35"
            }
        } elseif (preg_match('/^\(([^)]+)\)/', $norm, $matchParen)) {
            // e.g. (4.8-10.8) 10^3/uL -> extract 4.8-10.8
            $targetNorm = $matchParen[1];
        }

        $targetNorm = trim($targetNorm);

        // Clean result value (remove non-numeric chars except dot/minus)
        $numericVal = filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        // Case 1: Less than or equal to <= or <
        if (preg_match('/^<=\s*([0-9.]+)/', $targetNorm, $m)) {
            if (is_numeric($numericVal)) {
                return floatval($numericVal) > floatval($m[1]);
            }
        }
        if (preg_match('/^<\s*([0-9.]+)/', $targetNorm, $m)) {
            if (is_numeric($numericVal)) {
                return floatval($numericVal) >= floatval($m[1]);
            }
        }
        // Case 2: Greater than or equal to >= or >
        if (preg_match('/^>=\s*([0-9.]+)/', $targetNorm, $m)) {
            if (is_numeric($numericVal)) {
                return floatval($numericVal) < floatval($m[1]);
            }
        }
        if (preg_match('/^>\s*([0-9.]+)/', $targetNorm, $m)) {
            if (is_numeric($numericVal)) {
                return floatval($numericVal) <= floatval($m[1]);
            }
        }

        // Case 3: Range e.g. "14.0-18.0" or "14.0 - 18.0" or "4.5 - 8.0"
        if (preg_match('/([0-9.]+)\s*-\s*([0-9.]+)/', $targetNorm, $m)) {
            if (is_numeric($numericVal)) {
                $fVal = floatval($numericVal);
                $min = floatval($m[1]);
                $max = floatval($m[2]);
                return ($fVal < $min || $fVal > $max);
            }
        }

        // Case 4: Text matches (case-insensitive)
        $normLower = strtolower($targetNorm);
        $valLower = strtolower($val);

        if ($normLower === 'negatif') {
            return ($valLower !== 'negatif' && $valLower !== 'negative' && $valLower !== '-' && $valLower !== 'non reaktif' && $valLower !== 'non-reaktif');
        }

        if ($normLower === 'non reaktif' || $normLower === 'non-reaktif') {
            return ($valLower !== 'non reaktif' && $valLower !== 'non-reaktif' && $valLower !== 'negatif' && $valLower !== '-');
        }

        if ($normLower === 'normal') {
            return ($valLower !== 'normal' && $valLower !== 'dalam batas normal' && $valLower !== '-');
        }

        if (in_array($normLower, ['kuning', 'jernih'])) {
            return $valLower !== $normLower;
        }

        return false;
    }
}