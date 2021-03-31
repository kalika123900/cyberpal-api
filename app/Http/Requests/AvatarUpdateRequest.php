<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvatarUpdateRequest extends FormRequest
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
            'profile_picture' => 'required|url',
        ];
    }

    public function messages()
    {
        return [
            'profile_picture.required' => 'Profile Picture is required.',
        ];
    }
}
