<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreBuyAppartmentInstallmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'apartment_id' => 'nullable|exists:uptowns,id',
            'age' => 'required|integer|min:18',
            'identity_front_image' => 'required|string',
            'identity_back_image' => 'required|string',
            'city' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'monthly_income' => 'required|numeric|min:0',
            'monthly_installment' => 'nullable|numeric|min:0',
            'years_of_installment' => 'required|in:3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20',
            'deposit_percetage' => 'required|numeric|min:5|max:80',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => $errors->first(),
                'errors' => $errors
            ], 422)
        );
    }
}
