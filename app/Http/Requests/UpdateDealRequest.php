<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDealRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'fullname' => 'nullable',
            'nationality_id' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'developer_id' => 'nullable|exists:developers,id',
            'compound_id' => 'nullable|exists:compounds,id',
            'uptown_type_id' => 'nullable|exists:uptown_types,id',
            'number_of_units' => 'nullable|numeric|min:1',
            'status' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'fullname.string' => 'Full name must be a string',
            'fullname.max' => 'Full name must not exceed 255 characters',
            'nationality_id.string' => 'Nationality ID must be a string',
            'phone.string' => 'Phone must be a string',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email must not exceed 255 characters',
            'developer_id.exists' => 'Selected developer does not exist',
            'compound_id.exists' => 'Selected compound does not exist',
            'uptown_type_id.exists' => 'Selected uptown type does not exist',
            'number_of_units.numeric' => 'Number of units must be a number',
            'number_of_units.min' => 'Number of units must be at least 1',
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
