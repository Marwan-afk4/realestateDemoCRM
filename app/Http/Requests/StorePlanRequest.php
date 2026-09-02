<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'count_of_leads' => 'required|integer|min:1',
            'period_in_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('The Name field is required.'),
            'count_of_leads.required' => __('The Count Of Leads field is required.'),
            'count_of_leads.integer' => __('The Count Of Leads must be a number.'),
            'period_in_days.required' => __('The Period In Days field is required.'),
            'period_in_days.integer' => __('The Period In Days must be a number.'),
            'price.required' => __('The Price field is required.'),
            'price.numeric' => __('The Price must be a valid number.'),
            'discount_type.in' => __('The Discount Type must be either fixed or percentage.'),
            'discount_value.numeric' => __('The Discount Value must be a valid number.')
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
