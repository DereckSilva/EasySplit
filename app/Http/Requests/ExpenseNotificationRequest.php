<?php

namespace App\Http\Requests;

use App\Trait\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ExpenseNotificationRequest extends FormRequest
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
            'owner_expense'              => [Rule::requiredIf(!$this->input('intermediary_expense'))],
            'owner_expense.notification' => ['boolean', Rule::requiredIf($this->input('owner_expense'))],
            'owner_expense.expense'      => ['integer', Rule::requiredIf($this->input('owner_expense'))],

            'intermediary_expense'                         => [Rule::requiredIf(!$this->input('owner_expense'))],
            'intermediary_expense.email'                   => ['email', Rule::requiredIf($this->input('intermediary_expense'))],
            'intermediary_expense.expenses'                => ['array', Rule::requiredIf($this->input('intermediary_expense'))],
            'intermediary_expense.expenses.*.id'           => ['integer', Rule::requiredIf($this->input('intermediary_expense'))],
            'intermediary_expense.expenses.*.notification' => ['boolean', Rule::requiredIf($this->input('intermediary_expense'))],
        ];
    }

    public function messages(): array {
        return [
            'owner_expense.required'              => 'O campo owner_expense é obrigatório quando não é informado os intermediários.',
            'owner_expense.notification.required' => 'Você deve informar se deseja receber notificações.',
            'owner_expense.notification.boolean'  => 'O campo de notificação do proprietário da despesa deve ser verdadeiro ou falso.',
            'owner_expense.expense.required'      => 'A identificação da despesa é obrigatória.',
            'owner_expense.expense.integer'       => 'O campo de expense deve ser inteiro.',
            
            'intermediary_expense.required'                         => 'O campo intermediary_expense é obrigatório quando o owner não é informado.',
            'intermediary_expense.email.email'                      => 'O e-mail do intermediário deve ser um endereço de e-mail válido.',
            'intermediary_expense.email.required'                   => 'O e-mail do intermediário é obrigatório quando o campo está presente.',
            'intermediary_expense.expenses.array'                   => 'O campo de despesas do intermediário deve ser um array.',
            'intermediary_expense.expenses.required'                => 'As despesas do intermediário são obrigatórias quando o campo está presente.',
            'intermediary_expense.expenses.*.id.integer'            => 'O ID de cada despesa do intermediário deve ser um número inteiro.',
            'intermediary_expense.expenses.*.id.required'           => 'O ID de cada despesa do intermediário é obrigatório quando o campo está presente.',
            'intermediary_expense.expenses.*.notification.boolean'  => 'O campo de notificação deve ser verdadeiro ou falso para cada despesa do intermediário.',
            'intermediary_expense.expenses.*.notification.required' => 'O campo de notificação é obrigatório para cada despesa do intermediário quando o campo está presente.',
        ];
    }

    public function failedValidation(Validator $validator): HttpResponseException {
        return $this->retornoExceptionErroRequest(false, 'Erro na atualização da notificação de uma conta', 422, $validator->errors());
    }
}
