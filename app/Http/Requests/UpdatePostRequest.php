<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'posts.*.dummy_post_id' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'posts.required' => 'Данные постов обязательны для заполнения!',
            'posts.array' => 'Пост должен быть массивом',
            'posts.*.title.required' => 'Заголовок обязателен',
            'posts.*.body.required' => 'Описание обязательно',
        ];
    }
}
