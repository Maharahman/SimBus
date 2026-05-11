<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        $currentUser = $this->user();
        $userId = $this->route('user');
        
        // Users can only update themselves, developers can update anyone
        return $currentUser->level === 'developer' || $currentUser->id == $userId;
    }

    public function rules()
    {
        $userId = $this->route('user');
        
        return [
            'name' => 'sometimes|required|string|max:255',
            'sur_name' => 'sometimes|required|string|max:255',
            'num' => 'sometimes|required|numeric|unique:users,num,' . $userId,
            'pass' => 'sometimes|nullable|min:6',
            'level' => 'sometimes|in:admin,user,developer',
        ];
    }
}
