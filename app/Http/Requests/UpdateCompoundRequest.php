<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompoundRequest extends FormRequest
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
            'developer_id.exists' => __('The selected Developer is invalid.'),
            'compound_name.required' => __('The Compound Name field is required.'),
            'image.string' => __('The Image must be a string.'),
            'commission_percentage.required' => __('The Commission Percentage field is required.'),
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
