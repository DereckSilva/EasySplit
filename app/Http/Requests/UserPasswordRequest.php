<?php

namespace App\Http\Requests;

use App\Trait\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Password;

class UserPasswordRequest extends FormRequest
{

    use Request;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'            => ['required', 'email'],
            'current_password' => ['required', Password::min(8)->max(12)->numbers()->letters()->symbols()],
            'password'         => ['required', Password::min(8)->max(12)->numbers()->letters()->symbols()],
        ];
    }

    public function messages(): array {
        return [
            'email.required'            => 'O email é obrigatório',
            'password.required'         => 'A senha é obrigatória',
            'current_password.required' => 'A senha atual é obrigatória',

            'password.min'     => 'A senha precisa ter no mínimo 8 caracteres',
            'password.max'     => 'A senha pode ter no máximo 12 caracteres',
            'password.letters' => 'A senha precisa ter no mínimo uma letra',
            'password.numbers' => 'A senha precisa ter no mínimo um número',
            'password.symbols' => 'A senha precisa ter no mínimo um símbolo',

            'current_password.min'     => 'A senha precisa ter no mínimo 8 caracteres',
            'current_password.max'     => 'A senha pode ter no máximo 12 caracteres',
            'current_password.letters' => 'A senha precisa ter no mínimo uma letra',
            'current_password.numbers' => 'A senha precisa ter no mínimo um número',
            'current_password.symbols' => 'A senha precisa ter no mínimo um símbolo',

            'password.confirmed'        => 'A confirmação da senha não confere',
        ];
    }

    public function failedValidation(Validator $validator): HttpResponseException {
        return $this->retornoExceptionErroRequest(false, '', 404, $validator->errors());
    }
}
