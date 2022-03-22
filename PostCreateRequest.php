<?php

namespace App\Http\Requests;

use App\Rules\PostFiles;
use Illuminate\Foundation\Http\FormRequest;

class PostCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'files' => ['bail', 'required_without:body', 'nullable', 'array', 'max:10', new PostFiles],
            'files.*' => ['file', 'max:262144'],
            'body' => 'nullable|string|max:1000|required_without:files',
        ];
    }

    public function messages()
    {
        return [
            'files.*.max' => __('Sorry! Maximum allowed upload size is 256MB'),
        ];
    }
}
