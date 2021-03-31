<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required', 
            'last_name'  => 'required',
            'email' => 'required', 
            'phone' => 'required', 
            // 'industry' => '', 
            // 'organisation_size' => '', 
            // 'message' => '', 
            // 'user_id' => ''
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'We must know your first name.',
            'last_name.required' => 'We must know your last name.',
            'email.required'  => 'We must know your email address.',
            'phone.required' => 'We must know where we can contact you.'
        ];
    }
}
