<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'brocker_id' => 'required|exists:brockers,id',
            'plan_id' => 'required|exists:plans,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'receipt' => 'required',
            'status' => 'required|in:pending,approved,rejected',
        ];
    }

    public function messages()
    {
        return [
            'brocker_id.required' => 'Brocker ID is required.',
            'brocker_id.exists' => 'Brocker ID is invalid.',
            'plan_id.required' => 'Plan ID is required.',
            'plan_id.exists' => 'Plan ID is invalid.',
            'payment_method_id.required' => 'Payment Method ID is required.',
            'payment_method_id.exists' => 'Payment Method ID is invalid.',
            'receipt.required' => 'Receipt is required.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of the following: pending, approved, rejected.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new ValidationException($validator, response()->json([
                'status' => 'error',
                'message' => __('Validation failed'),
                'errors' => $validator->errors()
            ]));
        }

        throw new ValidationException($validator);
    }
}
