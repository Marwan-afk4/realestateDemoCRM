<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title_en' => 'required',
            'title_ar' => 'required',
            'image' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'title_en.required' => __('The English Title field is required.'),
            'title_ar.required' => __('The Arabic Title field is required.'),
            'image.required' => __('The Image field is required.'),
            'image.string' => __('The Image must be a string.')
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
