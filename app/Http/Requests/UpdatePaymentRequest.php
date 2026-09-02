<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'exists:users,id',
            'brocker_id' => 'exists:brockers,id',
            'plan_id' => 'exists:plans,id',
            'payment_method_id' => 'exists:payment_methods,id',
            'receipt' => 'required|string',
            'status' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'user_id.exists' => __('The selected User is invalid.'),
            'brocker_id.exists' => __('The selected Brocker is invalid.'),
            'plan_id.exists' => __('The selected Plan is invalid.'),
            'payment_method_id.exists' => __('The selected Payment Method is invalid.'),
            'receipt.required' => __('The Receipt field is required.'),
            'receipt.string' => __('The Receipt must be a string.'),
            'status.required' => __('The Status field is required.')
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
