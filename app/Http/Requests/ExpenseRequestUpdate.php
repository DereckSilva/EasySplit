<?php

namespace App\Http\Requests;

use App\Trait\ResponseHttp;

class ExpenseRequestUpdate extends ExpenseRequest
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
        $rulesExpenseCreate = parent::rules();
        unset($rulesExpenseCreate['payer_id']);
        $rulesExpenseUpdate = [
            'id' => ['required', 'integer', 'exists:expenses,id']
        ];

        return array_merge($rulesExpenseCreate, $rulesExpenseUpdate);
    }

    public function messages(): array {
        $messagesExpenseCreate = parent::messages();
        unset($messagesExpenseCreate['payer_id']);
        $messagesExpenseUpdate = [
            'id.required' => 'O id é obrigatório',
            'id.integer' => 'O id deve ser um número',
            'id.exists' => 'O id informado não existe',
        ];

        return array_merge($messagesExpenseCreate, $messagesExpenseUpdate);
    }
}
