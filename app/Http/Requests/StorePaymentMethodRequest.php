<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentMethodRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'method_name' => 'required|string|max:255|unique:payment_methods,method_name',
            'image' => 'required',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'method_name.required' => 'The method name is required.',
            'method_name.unique' => 'The method name must be unique.',
            'image.required' => 'The image is required.',
            'status.required' => 'The status is required.',
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
