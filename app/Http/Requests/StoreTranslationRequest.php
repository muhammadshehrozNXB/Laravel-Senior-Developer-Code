<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTranslationRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'language_id' => 'required|numeric',
            'meta_key' => 'required|string|unique:translations,meta_key',
            'content' => 'required|string',
            'tags' => 'array',
        ];
    }
}
