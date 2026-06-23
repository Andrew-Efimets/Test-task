<?php

namespace App\Http\Requests\Chats;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => 'required|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'Message body is required.',
            'body.max'      => 'Message body must be less than 5000 characters.',
        ];
    }
}
