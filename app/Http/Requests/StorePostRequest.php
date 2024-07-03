<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'posts' => 'required|array',
            'posts.*.title' => 'required|string|max:255',
            'posts.*.body' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'posts.required' => 'Данные постов обязательны!',
            'posts.array' => 'Пост должен быть массивом',
            'posts.*.title.required' => 'Заголовок обязателен',
            'posts.*.body.required' => 'Описание обязательно',
        ];
    }
}
