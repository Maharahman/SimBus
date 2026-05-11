<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'category' => 'nullable|in:app_issue,registration',
            'num' => 'nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
            'subject.required' => 'Ticket subject is required.',
            'message.required' => 'Ticket message cannot be empty.',
        ];
    }
}
