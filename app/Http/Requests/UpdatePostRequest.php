<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Убедитесь, что авторизация разрешена
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
        ];
    }

    public function messages()
    {
        return [
            'title.sometimes' => 'Название обязательно',
            'body.sometimes' => 'Описание обязательно',
        ];
    }
}
