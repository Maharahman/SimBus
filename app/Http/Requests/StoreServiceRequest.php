<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->level !== 'user';
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Service name is required.',
            'price.required' => 'Service price is required.',
            'price.numeric' => 'Price must be a valid number.',
        ];
    }
}