<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeveloperRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name_en' => 'required|string|max:255|unique:developers,name_en',
            'name_ar' => 'required|string|max:255|unique:developers,name_ar',
            'email' => 'nullable|email|max:255|unique:developers,email',
            'units' => 'nullable|integer|min:0',
            'total_deals' => 'nullable|integer|min:0',
            'total_profit' => 'nullable|numeric|min:0',
            'deals_done' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description_en' => 'nullable|string|max:1000',
            'description_ar' => 'nullable|string|max:1000',
            'places' => 'nullable|array',
            'places.*' => 'nullable|string|max:255',
            'salesmen' => 'nullable|array',
            'salesmen.*.name' => 'nullable|string|max:255',
            'salesmen.*.phone' => 'nullable|string|max:20'
        ];
    }

    public function messages()
    {
        return [
            'name_en.required' => 'The English Name field is required.',
            'name_en.unique' => 'The English Name has already been taken.',
            'name_ar.required' => 'The Arabic Name field is required.',
            'name_ar.unique' => 'The Arabic Name has already been taken.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'units.integer' => 'The units must be an integer.',
            'units.min' => 'The units must be at least 0.',
            'total_deals.integer' => 'The total deals must be an integer.',
            'total_deals.min' => 'The total deals must be at least 0.',
            'total_profit.numeric' => 'The total profit must be a number.',
            'total_profit.min' => 'The total profit must be at least 0.',
            'deals_done.integer' => 'The deals done must be an integer.',
            'deals_done.min' => 'The deals done must be at least 0.',
            'start_date.date' => 'The start date must be a valid date.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The image may not be greater than 2MB.',
            'description_en.max' => 'The English Description may not be greater than 1000 characters.',
            'description_ar.max' => 'The Arabic Description may not be greater than 1000 characters.'
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
