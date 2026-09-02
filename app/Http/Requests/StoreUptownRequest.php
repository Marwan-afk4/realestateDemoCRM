<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUptownRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'developer_id' => 'required|exists:developers,id',
            'compound_id' => 'required|exists:compounds,id',
            'uptown_type_id' => 'required|exists:uptown_types,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'strat_price' => 'required|numeric|min:0',
            'delivery_date' => 'required|date',
            'status' => 'required|in:available,sold,reserved',
            'space' => 'required|numeric|min:0',
            'bathroom' => 'required|integer|min:0',
            'bed' => 'required|integer|min:0',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'floor_plan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'master_plan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'commission_price' => 'nullable|numeric|min:0',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'cash' => 'nullable|boolean',
            'installment' => 'nullable|boolean',
            'installment_years' => 'nullable|integer|min:1|max:50',
            'installment_plan' => 'nullable|in:monthly,yearly',
            'installment_price' => 'nullable|numeric|min:0',
            'type' => 'required|in:rent,buy',
            'unit_images' => 'nullable|array|max:10',
            'unit_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'developer_id.required' => 'Please select a developer.',
            'developer_id.exists' => 'The selected developer is invalid.',
            'compound_id.required' => 'Please select a compound.',
            'compound_id.exists' => 'The selected compound is invalid.',
            'uptown_type_id.required' => 'Please select an uptown type.',
            'uptown_type_id.exists' => 'The selected uptown type is invalid.',
            'name_en.required' => 'The English name is required.',
            'name_ar.required' => 'The Arabic name is required.',
            'strat_price.required' => 'The starting price is required.',
            'strat_price.numeric' => 'The starting price must be a number.',
            'delivery_date.required' => 'The delivery date is required.',
            'delivery_date.date' => 'Please enter a valid delivery date.',
            'status.required' => 'Please select a status.',
            'status.in' => 'The status must be available, sold, or reserved.',
            'installment_plan.in' => 'The installment plan must be either monthly or yearly.',
            'type.required' => 'Please select whether the unit is for rent or buy.',
            'type.in' => 'The type must be either rent or buy.',
            'space.required' => 'The space is required.',
            'space.numeric' => 'The space must be a number.',
            'bathroom.required' => 'The number of bathrooms is required.',
            'bathroom.integer' => 'The number of bathrooms must be a whole number.',
            'bed.required' => 'The number of bedrooms is required.',
            'bed.integer' => 'The number of bedrooms must be a whole number.',
            'floor_plan_image.image' => 'The floor plan must be an image.',
            'floor_plan_image.mimes' => 'The floor plan must be a file of type: jpeg, png, jpg, gif.',
            'floor_plan_image.max' => 'The floor plan may not be greater than 2MB.',
            'master_plan_image.image' => 'The master plan must be an image.',
            'master_plan_image.mimes' => 'The master plan must be a file of type: jpeg, png, jpg, gif.',
            'master_plan_image.max' => 'The master plan may not be greater than 2MB.',
            'cash.required' => 'Please select cash payment option.',
            'cash.boolean' => 'Cash payment must be yes or no.',
            'installment.required' => 'Please select installment payment option.',
            'installment.boolean' => 'Installment payment must be yes or no.',
            'installment_years.required' => 'The installment years is required.',
            'installment_years.integer' => 'The installment years must be a whole number.',
            'unit_images.array' => 'Unit images must be an array.',
            'unit_images.max' => 'You can upload maximum 10 unit images.',
            'unit_images.*.image' => 'Each unit image must be an image file.',
            'unit_images.*.mimes' => 'Unit images must be of type: jpeg, png, jpg, gif.',
            'unit_images.*.max' => 'Each unit image may not be greater than 2MB.',
        ];
    }
}
