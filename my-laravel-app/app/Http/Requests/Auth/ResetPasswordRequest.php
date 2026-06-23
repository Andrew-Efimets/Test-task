<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Traits\HasEmailBlindIndex;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'token'                 => 'required|string',
            'email'                 => 'required|email',
            'email_blind'           => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
        ];
    }
}
