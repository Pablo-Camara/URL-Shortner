<?php

namespace App\Http\Requests;

use App\Rules\DestinationUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveLinkRequest extends FormRequest
{
    public const RESERVED = ['app', 'api', 'login', 'register', 'logout', 'up', 'build', 'storage', 'favicon', 'robots', 'entrar', 'criar-conta', 'os-meus-links', 'contacte', 'meu-perfil', 'painel-admin', 'criar-link-personalizado', 'confirmar-email', 'alterar-palavra-passe'];

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'url' => ['required', 'string', 'max:2048', new DestinationUrl],
            'alias' => $this->isMethod('POST') ? ['nullable', 'string', 'min:3', 'max:40', 'regex:/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', Rule::notIn(self::RESERVED)] : ['prohibited'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'version' => $this->isMethod('PATCH') ? ['required', 'integer', 'min:1'] : ['prohibited'],
        ];
    }
}
