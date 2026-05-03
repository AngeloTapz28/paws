<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdminOrStaff();
    }

    public function rules(): array
    {
        $petId = $this->route('pet')?->id;

        return [
            'name'            => ['required', 'string', 'max:100'],
            'pet_category_id' => ['required', 'exists:pet_categories,id'],
            'breed_id'        => ['nullable', 'exists:breeds,id'],
            'gender'          => ['required', 'in:male,female,unknown'],
            'date_of_birth'   => ['nullable', 'date', 'before:today'],
            'weight'          => ['nullable', 'numeric', 'min:0', 'max:500'],
            'size'            => ['nullable', 'in:tiny,small,medium,large,extra_large'],
            'color'           => ['nullable', 'string', 'max:100'],
            'description'     => ['nullable', 'string', 'max:2000'],
            'special_needs'   => ['nullable', 'string', 'max:1000'],
            'is_vaccinated'   => ['boolean'],
            'is_neutered'     => ['boolean'],
            'is_microchipped' => ['boolean'],
            'adoption_fee_type' => ['required', 'in:fixed,donation,free'],
            'adoption_fee'    => ['nullable', 'numeric', 'min:0'],
            'primary_image'   => [
                $petId ? 'nullable' : 'required',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048', // 2MB
            ],
            'images.*'        => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'pet_category_id.required' => 'Please select a pet category.',
            'pet_category_id.exists'   => 'Selected category is invalid.',
            'primary_image.required'   => 'Please upload at least one photo.',
            'primary_image.max'        => 'Image must not exceed 2MB.',
        ];
    }
}