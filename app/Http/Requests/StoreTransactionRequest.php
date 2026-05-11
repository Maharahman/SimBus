<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->level !== 'user';
    }

    public function rules()
    {
        return [
            'services' => 'required_without:items|array',
            'services.*.id' => 'required|exists:services,id',
            'services.*.qty' => 'required|integer|min:1',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|exists:inventory,id',
            'items.*.qty' => 'required_with:items.*.id|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'services.required_without' => 'You must provide at least services or items.',
            'services.*.id.required' => 'Each service must have an ID.',
            'services.*.qty.required' => 'Service quantity is required.',
        ];
    }
}