<?php

namespace App\Filters\Patient;

use App\Support\SearchHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PatientFilter
{
    public function __construct(protected Request $request) {}

    public function apply(Builder $query): Builder
    {
        // Global search (nama, No. RM, NIK) - case-insensitive
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                SearchHelper::whereLike($q, 'name', $search, 'and');
                SearchHelper::whereLike($q, 'patient_code', $search, 'or');
                SearchHelper::whereLike($q, 'nik', $search, 'or');
            });
        }

        // Per-column filters (Enter manual, data-table) - case-insensitive
        foreach (['patient_code', 'nik', 'name', 'phone'] as $field) {
            $key = 'filter_'.$field;
            if ($this->request->filled($key)) {
                SearchHelper::whereLike($query, $field, $this->request->input($key));
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
