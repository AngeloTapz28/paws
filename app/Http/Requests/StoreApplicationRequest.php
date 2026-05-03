<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdopter();
    }

    public function rules(): array
    {
        return [
            'pet_id'                => ['required', 'exists:pets,id'],
            'applicant_full_name'   => ['required', 'string', 'max:200'],
            'applicant_email'       => ['required', 'email', 'max:200'],
            'applicant_phone'       => ['required', 'string', 'max:20'],
            'applicant_address'     => ['required', 'string', 'max:500'],
            'housing_type'          => ['required', 'string', 'in:house,apartment,condo,other'],
            'has_yard'              => ['boolean'],
            'has_other_pets'        => ['boolean'],
            'other_pets_details'    => ['nullable', 'required_if:has_other_pets,1', 'string', 'max:500'],
            'has_children'          => ['boolean'],
            'children_ages'         => ['nullable', 'string', 'max:200'],
            'reason_for_adopting'   => ['required', 'string', 'min:50', 'max:2000'],
            'experience_with_pets'  => ['nullable', 'string', 'max:1000'],
            'occupation'            => ['nullable', 'string', 'max:200'],
            'working_hours'         => ['nullable', 'string', 'max:200'],
            'emergency_contact'     => ['nullable', 'string', 'max:300'],
            'additional_notes'      => ['nullable', 'string', 'max:1000'],
        ];
    }
}