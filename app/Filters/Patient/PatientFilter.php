<?php

namespace App\Filters\Patient;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PatientFilter
{
    public function __construct(protected Request $request) {}

    public function apply(Builder $query): Builder
    {
        // Global search (nama, No. RM, NIK)
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('patient_code', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Per-column filters (Enter manual, data-table)
        foreach (['patient_code', 'nik', 'name', 'phone'] as $field) {
            $key = 'filter_'.$field;
            if ($this->request->filled($key)) {
                $query->where($field, 'like', '%'.$this->request->input($key).'%');
            }
        }

        if ($this->request->filled('filter_gender')) {
            $query->where('gender', $this->request->filter_gender);
        }

        // Legacy support
        if ($this->request->filled('filter_patient_code')) {
            // already handled via patient_code loop, but keep for compat
        }

        return $query;
    }

    public function perPage(): int
    {
        $perPage = (int) $this->request->input('per_page', 10);
        return in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
    }
}
