<?php

namespace App\Http\Requests\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!empty(auth()->user())) return true;
        else return false;
    }

    public function rules()
    {
        return [
            'name' => 'required|min:4',
            'phone' => 'required|min:10|unique:users,phone,'.$this->user()->id,
        ];
    }

    public function messages()
    {
        return [
            'unique' => 'The :attribute already been registered.',
            'phone.required' => 'Phone field is required.',
            'name.required'=> 'Name field is required',
        ];
    }
}
