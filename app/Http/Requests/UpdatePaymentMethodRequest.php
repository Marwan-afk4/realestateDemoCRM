<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentMethodRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'method_name' => 'nullable|string|max:255|unique:payment_methods,method_name,' . $this->payment_method->id,
            'image' => 'nullable',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'method_name.unique' => 'The method name must be unique.',
            'status.in' => 'The selected status is invalid. Allowed values are active or inactive.',
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
