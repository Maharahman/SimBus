<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->level === 'developer';
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sur_name' => 'required|string|max:255',
            'num' => 'required|numeric|unique:users,num',
            'pass' => 'required|min:6',
            'level' => 'required|in:admin,user',
        ];
    }

    public function messages()
    {
        return [
            'num.unique' => 'This phone number is already registered.',
            'pass.min' => 'Password must be at least 6 characters.',
        ];
    }
}
