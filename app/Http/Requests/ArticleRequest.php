<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'POST' => [
                'blog_id' => 'required|int|exists:blogs,id',
                'name' => 'required|string|min:3|unique:articles,name',
                'text' => 'required|string|min:80',
            ],
            'PATCH' => [
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    Rule::unique('articles', 'name')->ignore($this->get('article_id'))
                ],
                'text' => 'required|string|min:80',
                'article_id' => 'required|exists:articles,id|exclude'
            ],
            'PUT' => [
                'name' => [
                    'nullable',
                    'string',
                    'min:3',
                    Rule::unique('articles', 'name')->ignore($this->get('article_id'))
                ],
                'text' => 'nullable|string|min:80',
                'article_id' => 'required|exists:articles,id|exclude'
            ],
            'DELETE' => [
                'article_id' => 'required|exists:articles,id|exclude'
            ]
        };
    }

    public function prepareForValidation()
    {
        if (in_array($this->method(), ['PUT', 'DELETE'])) {
            $this->merge([
                'article_id' => (int)request()->segment(3)
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'article_id.exists' => __('Article not found')
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors(), 'success' => false], 422)
        );
    }
}
