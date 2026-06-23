<?php

namespace App\Http\Requests\Traits;

use App\Models\User;

trait HasEmailBlindIndex
{
    /**
     * Автоматическая подготовка слепого индекса для валидации.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('email') && !empty($this->input('email'))) {
            $this->merge([
                'email_blind' => User::generateEmailBlindIndex($this->input('email'))
            ]);
        }
    }
}
