<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class KickPlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'يجب اختيار اللاعب الذي تريد طرده.',
            'user_id.exists'   => 'هذا اللاعب غير موجود.',
        ];
    }
}
