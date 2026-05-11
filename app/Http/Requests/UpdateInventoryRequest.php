<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->level !== 'user';
    }

    public function rules()
    {
        return [
            'item_name' => 'sometimes|required|string|max:255',
            'item_price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
        ];
    }
}