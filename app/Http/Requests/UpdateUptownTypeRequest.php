<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUptownTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name_en' => 'nullable|string|unique:uptown_types,name_en,'.$this->uptown_type->id,
            'name_ar' => 'nullable|string|unique:uptown_types,name_ar,'.$this->uptown_type->id,
            'status' => 'nullable|in:active,inactive'
        ];
    }

    public function messages()
    {
        return [
            'name_en.string' => __('The English Name must be a string.'),
            'name_en.unique' => __('The English Name has already been taken.'),
            'name_ar.string' => __('The Arabic Name must be a string.'),
            'name_ar.unique' => __('The Arabic Name has already been taken.'),
            'status.in' => __('The selected Status is invalid.')
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
