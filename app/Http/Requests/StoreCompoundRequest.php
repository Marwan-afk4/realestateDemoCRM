<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompoundRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'developer_id' => 'required|exists:developers,id',
            'compound_name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'units' => 'required|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|between:0,100',
            'favourite' => 'nullable|in:0,1',
        ];
    }

    protected function prepareForValidation()
    {
        // Convert favourite to boolean
        $this->merge([
            'favourite' => $this->has('favourite') && $this->favourite == '1' ? 1 : 0,
        ]);
    }

    public function messages()
    {
        return [
            'developer_id.required' => __('The Developer ID field is required.'),
            'developer_id.exists' => __('The selected Developer ID is invalid.'),
            'compound_name.required' => __('The Compound Name field is required.'),
            'image.required' => __('The Image field is required.'),
            'units.numeric' => __('The Units field must be a number.'),
            'commission_percentage.numeric' => __('The Commission Percentage field must be a number.'),
            'commission_percentage.between' => __('The Commission Percentage field must be between 0 and 100.'),
            'favourite.boolean' => __('The Favourite field must be a boolean value.'),
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
