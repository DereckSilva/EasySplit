<?php

namespace App\Http\Requests;

use App\Trait\ResponseHttp;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{

    use ResponseHttp;

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
            'email'    => ['required', 'email'],
            'password' => ['required', Password::min(8)->max(12)->numbers()->letters()->symbols()]
        ];
    }

    public function messages(): array {
        return [
            'email.required'    => 'O e-mail é obrigatório para realizar o login',
            'password.required' => 'A senha é obrigatória para realizar o login',

            'password.min'     => 'A senha precisa ter no mínimo 8 caracteres',
            'password.max'     => 'A senha pode ter no máximo 12 caracteres',
            'password.letters' => 'A senha precisa ter no mínimo uma letra',
            'password.numbers' => 'A senha precisa ter no mínimo um número',
            'password.symbols' => 'A senha precisa ter no mínimo um símbolo',
        ];
    }

    public function failedValidation(Validator $validator): HttpResponseException {
        return $this->retornoExceptionErroRequest(false, 'Erro de validação', 400, $validator->errors());
    }
}
