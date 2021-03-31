<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorsSignupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'vendor_type' => 'required', 
            'name' => 'required', 
            'email' => 'required|unique:users', 
            'phone' => 'required|unique:users', 
            'password' => 'required',
            // 'business_name' => 'required', 
            'category_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'vendor_type.required' => 'Please select your business type.',
            'name.required'  => 'We must know your name.',
            'phone.required' => 'We must know where we can contact you.',
            'email.required'  => 'We must know your email address.',
            'password.required'  => 'Please select a password',
            // 'business_name.required'  => 'We must know your business name.',
            'category_id' => 'Please select your serving category',
        ];
    }
}
