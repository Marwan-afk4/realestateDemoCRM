<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            // 'password' => 'nullable|string|min:6',
            'email' => 'nullable|unique:users,email,'.$this->user->id,
            'phone' => 'nullable|unique:users,phone,'.$this->user->id,
            'qualification' => 'nullable|string',
            'experience_year' => 'nullable|integer',
            'governce' => 'nullable|string',
            'age' => 'nullable|integer',
            'status' => 'nullable|in:active,inactive'
        ];
    }

    public function messages()
    {
        return [
            'first_name.string' => 'First name must be a string.',
            'last_name.string' => 'Last name must be a string.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'Email has already been taken.',
            'phone.unique' => 'Phone number has already been taken.',
            'qualification.string' => 'Qualification must be a string.',
            'experience_year.integer' => 'Experience year must be an integer.',
            'governce.string' => 'Governce must be a string.',
            'age.integer' => 'Age must be an integer.',
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
