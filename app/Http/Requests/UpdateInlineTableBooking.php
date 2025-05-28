<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateInlineTableBooking extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        // Define an empty array to store the rules
        $rules = [];

        // Check each input and add corresponding validation rules
        if ($this->has('guest_name')) {
            $rules['guest_name'] = 'required|min:3';
        }
        if ($this->has('pick_up_location')) {
            $rules['pick_up_location'] = 'required|min:3';
        }
        if ($this->has('flight_detail')) {
            $rules['flight_detail'] = 'required|max:50|min:3';
        }
        if ($this->has('drop_of_location')) {
            $rules['drop_of_location'] = 'required|min:3';
        }
        if ($this->has('client_instructions')) {
            $rules['client_instructions'] = 'required|min:3';
        }
        if ($this->has('driver_remark')) {
            $rules['driver_remark'] = 'required|min:3';
        }

        // Return the rules array
        return $rules;
    }
}
