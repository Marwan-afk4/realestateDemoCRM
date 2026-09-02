<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|string|min:6',
            'experience_year' => 'nullable|integer',
            'governce' => 'nullable|string',
            'age' => 'required|integer',
            'qualification' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.string' => 'First name must be a string.',
            'last_name.required' => 'Last name is required.',
            'last_name.string' => 'Last name must be a string.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'Email has already been taken.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'Phone number has already been taken.',
            'password.required' => 'Password is required.',
            'password.string' => 'Password must be a string.',
            'password.min' => 'Password must be at least 6 characters long.',
            'experience_year.integer' => 'Experience year must be an integer.',
            'governce.string' => 'Governce must be a string.',
            'age.required' => 'Age is required.',
            'age.integer' => 'Age must be an integer.',
            'qualification.string' => 'Qualification must be a string.',
            'status.in' => 'Status must be either active or inactive.',
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
