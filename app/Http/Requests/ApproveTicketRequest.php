<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveTicketRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->level === 'developer';
    }

    public function rules()
    {
        return [
            'decision' => 'sometimes|in:approve,reject',
            'level' => 'sometimes|in:admin,user',
            'reject_reason' => 'nullable|string|max:1000',
        ];
    }
}
