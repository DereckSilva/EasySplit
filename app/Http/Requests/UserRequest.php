<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;
use App\Trait\ResponseHttp as RequestTrait;

class UserRequest extends FormRequest
{

    use RequestTrait;

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
            'name'         => ['required', 'min:5'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'password'     => ['required', Password::min(8)->max(12)->letters()->numbers()->symbols(), 'confirmed' ],
            'phone_number' => ['required', 'unique:users,phone_number', 'numeric', 'min_digits:11', 'max_digits:14'],
            'birthdate'    => ['required', 'date'],
        ];
    }

    public function messages(): array {
        return [
            'name.required'       => 'O nome é obrigatório',
            'email.required'      => 'O email é obrigatório',
            'password.required'   => 'A senha é obrigatória',
            'birthdate.required' => 'A data de nascimento é obrigatória',
            'phone_number.required' => 'O telefone é obrigatório',

            'email.unique' => 'O e-mail informado já foi cadastrado.',
            'phone_number.unique' => 'O telefone informado já foi cadastrado.',

            'name.min'     => 'O nome precisa ter no mínimo 5 caracteres',
            'password.min' => 'A senha precisa ter no mínimo 8 caracteres',
            'phone_number.min_digits' => 'O telefone precisa ter no mínimo 11 caracteres',
            'phone_number.numeric' => 'O telefone precisa ter apenas números',

            'password.max'     => 'A senha pode ter no máximo 12 caracteres',
            'phone_number.max_digits' => 'O telefone precisa ter no máximo 14 caracteres',

            'password.letters'   => 'A senha precisa ter no mínimo uma letra',
            'password.numbers'   => 'A senha precisa ter no mínimo um número',
            'password.symbols'   => 'A senha precisa ter no mínimo um símbolo',
            'password.confirmed' => 'A password_confirmation da senha não confere ou não foi informada',

            'birthdate.date' => 'A data de nascimento é inválida ',
        ];
    }

    public function failedValidation(Validator $validator): HttpResponseException {
        return $this->retornoExceptionErroRequest(false, 'Erro de validação no cadastro do usuário', 422, $validator->errors());
    }
}
