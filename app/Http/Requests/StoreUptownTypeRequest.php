<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreUptownTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name_en' => 'required|string|unique:uptown_types,name_en',
            'name_ar' => 'required|string|unique:uptown_types,name_ar',
            'status' => 'required|in:active,inactive'
        ];
    }

    public function messages()
    {
        return [
            'name_en.required' => __('The English Name field is required.'),
            'name_en.string' => __('The English Name must be a string.'),
            'name_en.unique' => __('The English Name has already been taken.'),
            'name_ar.required' => __('The Arabic Name field is required.'),
            'name_ar.string' => __('The Arabic Name must be a string.'),
            'name_ar.unique' => __('The Arabic Name has already been taken.'),
            'status.in' => __('The selected Status is invalid.'),
            'status.required' => __('The Status field is required.')
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
