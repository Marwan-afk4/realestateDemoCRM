<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreDealRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'fullname' => 'required|string|max:255',
            'nationality_id' => 'required|string',
            'phone' => 'required|string|max:11',
            'email' => 'required|email|max:255',
            'developer_id' => 'required|exists:developers,id',
            'compound_id' => 'required|exists:compounds,id',
            'uptown_type_id' => 'required|exists:uptown_types,id',
            'number_of_units' => 'required|numeric|min:1',
            'status' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'fullname.required' => 'Full name is required',
            'fullname.string' => 'Full name must be a string',
            'fullname.max' => 'Full name must not exceed 255 characters',
            'nationality_id.required' => 'Nationality ID is required',
            'nationality_id.string' => 'Nationality ID must be a string',
            'phone.required' => 'Phone is required',
            'phone.string' => 'Phone must be a string',
            'phone.max' => 'Phone must not exceed 11 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email must not exceed 255 characters',
            'developer_id.required' => 'Developer is required',
            'developer_id.exists' => 'Selected developer does not exist',
            'compound_id.required' => 'Compound is required',
            'compound_id.exists' => 'Selected compound does not exist',
            'uptown_type_id.required' => 'Uptown type is required',
            'uptown_type_id.exists' => 'Selected uptown type does not exist',
            'number_of_units.required' => 'Number of units is required',
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
