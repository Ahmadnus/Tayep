<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class LeaveGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'game_id' => 'required|exists:games,id',
        ];
    }

    public function messages(): array
    {
        return [
            'game_id.required' => 'يجب إرسال رقم الغرفة.',
            'game_id.exists'   => 'هذه الغرفة غير موجودة.',
        ];
    }
}
