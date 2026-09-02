<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDeveloperRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $developerId = $this->route('developer')->id ?? null;

        return [
            'name_en' => 'required|string|max:255|unique:developers,name_en,' . $developerId,
            'name_ar' => 'required|string|max:255|unique:developers,name_ar,' . $developerId,
            'email' => 'nullable|email|max:255|unique:developers,email,' . $developerId,
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
            'name_en.required' => __('The English name field is required.'),
            'name_en.unique' => __('The English name has already been taken.'),
            'name_ar.required' => __('The Arabic name field is required.'),
            'name_ar.unique' => __('The Arabic name has already been taken.'),
            'email.email' => __('The email must be a valid email address.'),
            'email.unique' => __('The email has already been taken.'),
            'units.integer' => __('The units must be an integer.'),
            'units.min' => __('The units must be at least 0.'),
            'total_deals.integer' => __('The total deals must be an integer.'),
            'total_deals.min' => __('The total deals must be at least 0.'),
            'total_profit.numeric' => __('The total profit must be a number.'),
            'total_profit.min' => __('The total profit must be at least 0.'),
            'deals_done.integer' => __('The deals done must be an integer.'),
            'deals_done.min' => __('The deals done must be at least 0.'),
            'start_date.date' => __('The start date must be a valid date.'),
            'end_date.date' => __('The end date must be a valid date.'),
            'end_date.after_or_equal' => __('The end date must be after or equal to the start date.'),
            'image.image' => __('The file must be an image.'),
            'image.mimes' => __('The image must be a file of type: jpeg, png, jpg, gif.'),
            'image.max' => __('The image may not be greater than 2MB.'),
            'description_en.max' => __('The English description may not be greater than 1000 characters.'),
            'description_ar.max' => __('The Arabic description may not be greater than 1000 characters.')
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
