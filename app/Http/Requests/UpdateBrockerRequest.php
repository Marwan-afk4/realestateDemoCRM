<?php

namespace App\Http\Requests;

use App\Models\Brocker;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateBrockerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->getUserIdFromBrocker();

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$userId,
            'phone' => 'required|string|max:20',
            'plan_id' => 'nullable|exists:plans,id',
            'profit' => 'required|numeric',
            'number_of_deals' => 'required|integer',
            'deals_done' => 'required|integer',
            'comission_percentage' => 'required|numeric',
        ];
    }

    private function getUserIdFromBrocker()
    {
        try {
            $brocker = $this->route('brocker');

            if (! $brocker) {
                return null;
            }

            // If brocker is already a model instance
            if ($brocker instanceof Brocker) {
                return $brocker->user_id;
            }

            // If brocker is an ID, fetch the model
            if (is_numeric($brocker) || is_string($brocker)) {
                $brockerModel = Brocker::find($brocker);

                return $brockerModel?->user_id;
            }

            return null;
        } catch (\Exception $e) {
            // If there's any error, return null to skip unique validation
            return null;
        }
    }

    public function messages()
    {
        return [
            'first_name.required' => __('The First Name field is required.'),
            'last_name.required' => __('The Last Name field is required.'),
            'email.required' => __('The Email field is required.'),
            'email.email' => __('The Email must be a valid email address.'),
            'email.unique' => __('The Email has already been taken.'),
            'phone.required' => __('The Phone field is required.'),
            'plan_id.exists' => __('The selected Plan is invalid.'),
            'profit.required' => __('The Profit field is required.'),
            'number_of_deals.required' => __('The Number Of Deals field is required.'),
            'deals_done.required' => __('The Deals Done field is required.'),
            'comission_percentage.required' => __('The Comission Percentage field is required.'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new ValidationException($validator, response()->json([
                'status' => 'error',
                'message' => __('Validation failed'),
                'errors' => $validator->errors(),
            ]));
        }

        throw new ValidationException($validator);
    }
}
