<?php

namespace App\Http\Requests;

use App\Trait\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator as FacadesValidator;
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
            'name'                     => ['required', 'alpha_num'],
            'price'                    => ['required', 'decimal:2,2', 'numeric'],
            'parcels'                  => ['required', 'integer'],
            'payee_id'                 => ['required', 'integer'],
            'date_payment'             => ['required', 'date', 'after_or_equal:today'],
            'intermediary'             => ['boolean', Rule::requiredIf(is_array($this->input('intermediarys_id')))],
            'intermediarys_id'         => ['array', Rule::requiredIf($this->input('intermediary'))],
            'intermediarys_id.*.email' => ['required', 'email'],
        ];
    }

    public function messages(): array {
        return [
        'name.required'             => 'O campo nome é obrigatório.',
        'price.required'            => 'O campo preço é obrigatório.',
        'parcels.required'          => 'O campo parcelas é obrigatório.',
        'payee_id.required'         => 'O campo recebedor é obrigatório.',
        'date_payment.required'     => 'A data de pagamento é obrigatória.',
        'intermediarys_id.required' => 'A lista de intermediários deve ser informada quando o campo intermediary é true',
        'intermediary.required'     => 'Necessário informar o campo intermediary quando declarado a lista de intermediários',

        'parcels.integer'        => 'O campo parcelas deve ser um número inteiro.',
        'payee_id.integer'       => 'O campo recebedor deve ser um número inteiro.',
        'intermediary.boolean'   => 'O campo intermediário deve ser verdadeiro ou falso (booleano).',
        
        'intermediarys_id.array'   => 'O campo intermediários deve ser uma lista (array).',
        'intermediarys_id.*.email' => 'E-mail inválido dentro da lista de intermediários',
        
        'date_payment.date'           => 'A data de pagamento deve ser uma data válida.',
        'date_payment.after_or_equal' => 'A data de pagamento deve ser igual ou posterior ao dia de hoje',
        
        'price.decimal'          => 'O número mínimo e máximo é de 2 casas.',
        'price.numeric'          => 'O preço precisa ser um número',

        'name.alpha_num' => 'O nome deve conter apenas letras e/ou números'
    ];
    }

    public function failedValidation(Validator $validator): HttpResponseException {
        return $this->retornoExceptionErroRequest(false, 'Erro na criação de uma conta', 422, $validator->errors());
    }
}
