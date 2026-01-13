<?php

namespace App\Http\Requests;

use App\Trait\ResponseHttp;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
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
            'name'                     => ['required', 'alpha'],
            'price_total'              => ['required', 'decimal:0,2', 'numeric'],
            'parcels'                  => ['required', 'integer'],
            'payer_id'                 => ['required', 'integer', 'exists:users,id'],
            'payment_date'             => ['required', 'date', 'after_or_equal:today'],
            'intermediary'             => ['required', 'boolean', function ($attribute, $value, $fail) {
                if (!$value && $this->input('intermediaries')) {
                    $fail("O campo $attribute deve ser true quando os intermediários são informados.");
                }
            }],
            'intermediaries'            => ['array', Rule::requiredIf($this->input('intermediary'))],
            'intermediaries.*.email'    => ['required', 'email', 'exists:users,email'],
            'receive_notification'     => ['required', 'boolean'],
        ];
    }

    public function messages(): array {
        return [
        'name.required'                 => 'O campo nome é obrigatório.',
        'price_total.required'          => 'O campo preço é obrigatório.',
        'parcels.required'              => 'O campo parcelas é obrigatório.',
        'payer_id.required'             => 'O campo recebedor é obrigatório.',
        'payment_date.required'         => 'A data de pagamento é obrigatória.',
        'intermediary.required'         => 'O campo intermediary é obrigatório.',
        'intermediaries.required'       => 'A lista de intermediários deve ser informada quando o campo intermediary é true.',
        'receive_notification.required' => 'O campo notification é obrigatório.',

        'parcels.integer'              => 'O campo parcelas deve ser um número inteiro.',
        'payer_id.integer'             => 'O campo recebedor deve ser um número inteiro.',
        'payer_id.exists'              => 'O receber informado não está cadastrado',
        'intermediary.boolean'         => 'O campo intermediário deve ser verdadeiro ou falso.',
        'receive_notification.boolean' => 'O campo notification deve ser verdadeiro ou falso.',

        'intermediaries.array'          => 'O campo intermediários deve ser uma lista (array).',
        'intermediaries.*.email'        => 'E-mail inválido dentro da lista de intermediários.',
        'intermediaries.*.email.exists' => 'O e-mail do intermediário não foi cadastrado.',

        'payment_date.date'           => 'A data de pagamento deve ser uma data válida.',
        'payment_date.after_or_equal' => 'A data de pagamento deve ser igual ou posterior ao dia de hoje.',

        'price_total.decimal' => 'O número máximo é de 2 casas.',
        'price_total.numeric' => 'O preço precisa ser um número.',

        'name.alpha' => 'O nome deve conter apenas letras.'
    ];
    }

    public function failedValidation(Validator $validator): HttpResponseException {
        return $this->retornoExceptionErroRequest(false, 'Erro na criação de uma despesa', 422, $validator->errors());
    }
}
