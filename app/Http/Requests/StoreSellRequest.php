<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreSellRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'age' => 'required|integer|min:18',
            'identity_front_image' => 'required|string',
            'identity_back_image' => 'required|string',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'developer_id' => 'nullable|exists:developers,id',
            'compound_id' => 'nullable|exists:compounds,id',
            'detailed_pdf' => 'required|string',
            'price' => 'required|numeric|min:0',
            'installments' => 'required|boolean',
            'installments_years' => 'required_if:installments,true|nullable|integer|min:1',
            'installments_years_left' => 'required_if:installments,true|nullable|integer|min:0',
            'installments_total_price' => 'required_if:installments,true|nullable|numeric|min:0',
            'installments_price_per_year' => 'required_if:installments,true|nullable|numeric|min:0',
            'uptown_type_id' => 'required|exists:uptown_types,id',
            'unit_sub_type_id' => 'required|exists:unit_sub_types,id',
            
            // Property details
            'rooms_no' => 'nullable|integer|min:1',
            'bathrooms_no' => 'nullable|integer|min:1',
            'space' => 'nullable|numeric|min:1',
            'floor_no' => 'nullable|integer',
            'garden_area' => 'nullable|boolean',
            'garden_space' => 'nullable|numeric|min:0',
            'unit_plan' => 'nullable|string',
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm,video/3gpp|max:102400',
            'finishing' => 'required|in:finished,semi_finished,unfinished',
            'notes' => 'nullable|string',
            'execution_date' => 'required|in:immediately,6_months,1_year,2_years,3_years,4_years_or_more',
            
            // Allow for extra dynamic data
            'extra_data' => 'nullable|array',
            'images' => 'nullable|array', // key => base64
        ];
    }

    /**
     * Normalize empty notes to null so omitted and "" are stored consistently.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('notes') && $this->input('notes') === '') {
            $this->merge(['notes' => null]);
        }
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
