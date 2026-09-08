<?php

namespace App\Services\Mcu\Examination;

use App\Models\McuRegistration;
use Illuminate\Database\Eloquent\Collection;

class McuExaminationService
{
    public function getRegistrations(): Collection
    {
        return McuRegistration::with(['patient','package'])
            ->whereIn('status', ['registered','in_progress'])
            ->orderBy('registration_date','asc')
            ->get();
    }

    public function storeLab(McuRegistration $reg, array $results): void
    {
        foreach ($results as $examId => $value) {
            $exam = $reg->examLabs()->find($examId);
            if ($exam) $exam->update(['result_value'=>$value,'status'=>'completed']);
        }
        $reg->update(['status'=>'in_progress']);
    }

    public function storeRadiology(McuRegistration $reg, array $results, array $isNormal = []): void
    {
        foreach ($results as $examId => $value) {
            $exam = $reg->examRadiologies()->find($examId);
            if ($exam) {
                $isNorm = isset($isNormal[$examId]) && $isNormal[$examId]==1;
                $exam->update([
                    'result'=> json_encode(['is_normal'=>$isNorm,'findings'=>$value?:'']),
                    'status'=>'completed'
                ]);
            }
        }
        $reg->update(['status'=>'in_progress']);
    }

    public function storeAnamnesis(McuRegistration $reg, array $results): void
    {
        foreach ($results as $examId => $value) {
            $exam = $reg->examAnamneses()->find($examId);
            if ($exam) $exam->update(['result'=>$value,'status'=>'completed']);
        }
        $reg->update(['status'=>'in_progress']);
    }

    public function storePhysical(McuRegistration $reg, array $data): void
    {
        $result = $reg->physicalExamResult;
        if ($result) {
            $result->update([
                'vital_signs'=> $data['vital_signs'] ?? null,
                'head_and_neck'=> $data['head_and_neck'] ?? null,
                'thorax'=> $data['thorax'] ?? null,
                'abdomen'=> $data['abdomen'] ?? null,
                'urogenital'=> $data['urogenital'] ?? null,
                'extremities'=> $data['extremities'] ?? null,
                'others'=> $data['others'] ?? null,
                'doctor_id'=> auth()->id(),
                'status'=>'completed',
            ]);
        }
        $reg->update(['status'=>'in_progress']);
    }

    public function storeDoctor(McuRegistration $reg, array $results): void
    {
        foreach ($results as $examId => $value) {
            $exam = $reg->examMedicalActions()->find($examId);
            if ($exam) $exam->update(['result'=>$value,'doctor_id'=>auth()->id(),'status'=>'completed']);
        }
        $reg->update(['status'=>'in_progress']);
    }
}
