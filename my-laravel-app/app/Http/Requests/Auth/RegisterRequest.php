<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Traits\HasEmailBlindIndex;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;


class RegisterRequest extends FormRequest
{
    use HasEmailBlindIndex;
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
            'name' => 'required',
            'email' => 'required|email',
            'email_blind' => 'unique:users,email_blind',
            'password' => 'required|min:8',
            'remember' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',
            'remember.boolean' => 'Remember me',
        ];
    }
}
