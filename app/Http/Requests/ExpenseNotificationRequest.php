<?php

namespace App\Http\Requests;

use App\Trait\ResponseHttp;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ExpenseNotificationRequest extends FormRequest
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
            'owner.notification'        => [Rule::requiredIf($this->has('owner')), 'boolean'],
            'intermediary.notification' => [Rule::requiredIf($this->has('intermediary')), 'boolean'],
        ];
    }

    public function messages(): array {
        return [
            'owner.notification.required' => 'Deve-se informar se o owner deseja ou não receber notificações',
            'owner.notification.boolean'  => 'O campo notification deve ser verdadeiro ou falso',

            'intermediary.notification.boolean'  => 'O campo notification deve ser verdadeiro ou falso',
            'intermediary.notification.required' => 'Deve-se informase se o intermediário deseja ou não receber notificações'
        ];
    }

    public function failedValidation(Validator $validator): HttpResponseException {
        return $this->returnExceptionErrorRequest(false, 'Erro na atualização da notificação de uma conta', 422, $validator->errors());
    }
}
