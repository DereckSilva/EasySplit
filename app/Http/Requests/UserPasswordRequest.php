<?php

namespace App\Http\Requests;

use App\Trait\ResponseHttp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Password;

class UserPasswordRequest extends FormRequest
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
            'email'            => ['required_without:id', 'email', 'exists:users,email'],
            'id'               => ['required_without:email', 'exists:users,id', 'integer'],
            'password'         => ['required', Password::min(8)->max(12)->numbers()->letters()->symbols()],
        ];
    }

    public function messages(): array {
        return [
            'email.required_without' => 'O email é obrigatório quando o id não é informado',
            'email.email'            => 'O e-mail informado não é válido',
            'email.exists'           => 'O e-mail informado não pertence a nenhum usuário',

            'password.required'         => 'A senha é obrigatória',
            'current_password.required' => 'A senha atual é obrigatória',

            'password.min'       => 'A senha precisa ter no mínimo 8 caracteres',
            'password.max'       => 'A senha pode ter no máximo 12 caracteres',
            'password.letters'   => 'A senha precisa ter no mínimo uma letra',
            'password.numbers'   => 'A senha precisa ter no mínimo um número',
            'password.symbols'   => 'A senha precisa ter no mínimo um símbolo',
            'password.confirmed' => 'A confirmação da senha não confere',

            'id.exists'           => 'O usuário informado não existe',
            'id.integer'          => 'O id informado não é válido',
            'id.required_without' => 'O id é obrigatório quando o email não é informado',
        ];
    }

    public function failedValidation(Validator $validator): HttpResponseException {
        return $this->returnExceptionErrorRequest(false, '', 404, $validator->errors());
    }
}
