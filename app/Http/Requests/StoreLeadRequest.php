<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'marketing_agency_id' => 'nullable|exists:marketing_agencies,id',
            'uptown_id' => 'nullable|exists:uptowns,id',
            'interested_place' => 'required|string',
            'brocker_id' => 'nullable|exists:brockers,id',
            'lead_name' => 'required|string',
            'lead_phone' => 'required|string',
            'sales_man_name' => 'nullable|string',
            'sales_man_phone' => 'nullable|string',
            'status' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'marketing_agency_id.exists' => __('The selected Marketing Agency is invalid.'),
            'uptown_id.exists' => __('The selected Uptown is invalid.'),
            'interested_place.required' => __('The Interested Place field is required.'),
            'interested_place.string' => __('The Interested Place must be a string.'),
            'brocker_id.exists' => __('The selected Brocker is invalid.'),
            'lead_name.required' => __('The Lead Name field is required.'),
            'lead_phone.required' => __('The Lead Phone field is required.'),
            'sales_man_name.string' => __('The Sales Man Name must be a string.'),
            'sales_man_phone.string' => __('The Sales Man Phone must be a string.'),
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
