<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->level !== 'user';
    }

    public function rules()
    {
        return [
            'item_name' => 'required|string|max:255',
            'item_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'item_name.required' => 'Item name is required.',
            'item_price.required' => 'Item price is required.',
            'stock.required' => 'Stock quantity is required.',
        ];
    }
}