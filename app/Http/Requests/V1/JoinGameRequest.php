<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class JoinGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|exists:games,code',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'الرجاء إدخال كود الغرفة.',
            'code.exists'   => 'هذه الغرفة غير موجودة.',
        ];
    }
}
