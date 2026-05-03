<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isVet() || auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'examination_date'  => ['required', 'date', 'before_or_equal:today'],
            'diagnosis'         => ['nullable', 'string', 'max:500'],
            'symptoms'          => ['nullable', 'string', 'max:1000'],
            'treatment'         => ['nullable', 'string', 'max:1000'],
            'medications'       => ['nullable', 'string', 'max:1000'],
            'weight_at_exam'    => ['nullable', 'numeric', 'min:0'],
            'health_status'     => ['required', 'in:excellent,good,fair,poor,critical,healthy,sick,recovering,under_observation'],
            'fit_for_adoption'  => ['nullable', 'boolean'],
            'notes'             => ['nullable', 'string', 'max:2000'],
            'attachment'        => ['nullable', 'file', 'mimes:pdf,jpeg,png', 'max:5120'],
            'next_checkup_date' => ['nullable', 'date', 'after:today'],
        ];
    }
}