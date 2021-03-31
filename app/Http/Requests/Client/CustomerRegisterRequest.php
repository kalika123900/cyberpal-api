<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ];
    }

    public function messages()
    {
        return [
            'unique' => 'The :attribute already been registered.',
            'email.required' => 'Email field is required.',
            'password.required' => 'Password field is required.',
            'name.required'=> 'Name field is required',
            // 'email.unique' => 'Email address already exist.',
            'password.min'=> 'Password must be minimum of 6 characters'
        ];
    }
}
