<?php

namespace App\Http\Requests;

use App\Trait\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
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
            'name'                     => ['required', 'alpha'],
            'priceTotal'               => ['required', 'decimal:0,2', 'numeric'],
            'parcels'                  => ['required', 'integer'],
            'payee_id'                 => ['required', 'integer', 'exists:users,id'],
            'datePayment'              => ['required', 'date', 'after_or_equal:today'],
            'intermediary'             => ['required', 'boolean', function ($attribute, $value, $fail) {
                if (!$value && $this->input('intermediarys')) {
                    $fail("O campo $attribute deve ser true quando os intermediários são informados.");
                }
            }],
            'intermediarys'            => ['array', Rule::requiredIf($this->input('intermediary'))],
            'intermediarys.*.email'    => ['required', 'email', 'exists:users,email'],
            'receiveNotification'      => ['required', 'boolean'],
        ];
    }

    public function messages(): array {
        return [
        'name.required'                => 'O campo nome é obrigatório.',
        'priceTotal.required'          => 'O campo preço é obrigatório.',
        'parcels.required'             => 'O campo parcelas é obrigatório.',
        'payee_id.required'            => 'O campo recebedor é obrigatório.',
        'datePayment.required'         => 'A data de pagamento é obrigatória.',
        'intermediary.required'        => 'O campo intermediary é obrigatório.',
        'intermediarys.required'    => 'A lista de intermediários deve ser informada quando o campo intermediary é true.',
        'receiveNotification.required' => 'O campo notification é obrigatório.',

        'parcels.integer'             => 'O campo parcelas deve ser um número inteiro.',
        'payee_id.integer'            => 'O campo recebedor deve ser um número inteiro.',
        'payee_id.exists'            => 'O receber informado não está cadastrado',
        'intermediary.boolean'        => 'O campo intermediário deve ser verdadeiro ou falso.',
        'receiveNotification.boolean' => 'O campo notification deve ser verdadeiro ou falso.',
        
        'intermediarys.array'   => 'O campo intermediários deve ser uma lista (array).',
        'intermediarys.*.email' => 'E-mail inválido dentro da lista de intermediários.',
        'intermediarys.*.email.exists' => 'O e-mail do intermediário não foi cadastrado.',
        
        'datePayment.date'           => 'A data de pagamento deve ser uma data válida.',
        'datePayment.after_or_equal' => 'A data de pagamento deve ser igual ou posterior ao dia de hoje.',
        
        'priceTotal.decimal'          => 'O número máximo é de 2 casas.',
        'priceTotal.numeric'          => 'O preço precisa ser um número.',

        'name.alpha' => 'O nome deve conter apenas letras.'
    ];
    }

    public function failedValidation(Validator $validator): HttpResponseException {
        return $this->retornoExceptionErroRequest(false, 'Erro na criação de uma conta', 422, $validator->errors());
    }
}
