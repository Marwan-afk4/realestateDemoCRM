<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreBrockerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
            'plan_id' => 'nullable|exists:plans,id',
            'profit' => 'required|numeric',
            'number_of_deals' => 'nullable|integer',
            'deals_done' => 'nullable|integer',
            'comission_percentage' => 'nullable|numeric'
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => __('The First Name field is required.'),
            'last_name.required' => __('The Last Name field is required.'),
            'email.required' => __('The Email field is required.'),
            'email.email' => __('The Email must be a valid email address.'),
            'email.unique' => __('The Email has already been taken.'),
            'phone.required' => __('The Phone field is required.'),
            'plan_id.exists' => __('The selected Plan is invalid.'),
            'profit.required' => __('The Profit field is required.'),
            'number_of_deals.required' => __('The Number Of Deals field is required.'),
            'deals_done.required' => __('The Deals Done field is required.'),
            'comission_percentage.required' => __('The Comission Percentage field is required.')
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
