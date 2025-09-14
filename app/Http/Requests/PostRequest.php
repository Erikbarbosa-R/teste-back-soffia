<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|uuid|exists:users,id',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:50',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'O campo título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'content.required' => 'O campo conteúdo é obrigatório.',
            'author.required' => 'O campo autor é obrigatório.',
            'author.integer' => 'O autor deve ser um UUID válido.',
            'author.exists' => 'O autor selecionado não existe.',
            'tags.array' => 'As tags devem ser um array.',
            'tags.*.string' => 'Cada tag deve ser uma string.',
            'tags.*.max' => 'Cada tag não pode ter mais de 50 caracteres.',
        ];
    }
}




