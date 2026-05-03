<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdminOrStaff();
    }

    public function rules(): array
    {
        return [
            'adoption_application_id' => ['nullable', 'exists:adoption_applications,id'],
            'payer_id'                => ['nullable', 'exists:users,id'],
            'type'                    => ['required', 'in:adoption_fee,donation,medical_fee,other'],
            'amount'                  => ['required', 'numeric', 'min:1'],
            'method'                  => ['required', 'in:cash,bank_transfer,gcash,maya,credit_card,check,other'],
            'status'                  => ['required', 'in:pending,completed,failed,refunded,cancelled'],
            'paid_at'                 => ['nullable', 'date'],
            'proof_of_payment'        => ['nullable', 'file', 'mimes:jpeg,png,pdf', 'max:5120'],
            'notes'                   => ['nullable', 'string', 'max:500'],
        ];
    }
}