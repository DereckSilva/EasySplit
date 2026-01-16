<?php

namespace App\Http\Requests;

use App\Trait\ResponseHttp;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserDeleteRequest extends FormRequest
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
            'id'    => 'required_without:email|exists:users,id',
            'email' => 'required_without:id|email|exists:users,email'
        ];
    }

    public function messages(): array {
        return [
            'id.required_without' => 'O id é obrigatório quando o email não é informado',
            'id.exists' => 'O id informado não está cadastrado',

            'email.required_without' => 'O email é obrigatório quando o id não é informado',
            'email.email' => 'O email informado não é válido',
            'email.exists' => 'O email informado não está cadastrado'
        ];
    }

    public function failedValidation(Validator $validator): HttpResponseException {
        return $this->retornoExceptionErroRequest(false, 'Erro de validação no cadastro do usuário', 422, $validator->errors());
    }
}
