<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectsFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service_type' => 'required',
            // 'name' => 'required', 
            // 'email' => 'required', 
            // 'phone' => 'required', 
            'budget' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'service_type.required' => 'Please select a required service',
            // 'name.required'  => 'We must know your name.',
            // 'phone.required' => 'We must know where we can contact you.',
            // 'email.required'  => 'We must know your email address.',
            'budget.required'  => 'Please select your budget',
        ];
    }
}
