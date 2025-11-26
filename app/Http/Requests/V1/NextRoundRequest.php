<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class NextRoundRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
   public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'gameId' => 'required|integer|exists:games,id',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), [
            'gameId' => $this->route('gameId'),
        ]);
    }
}
