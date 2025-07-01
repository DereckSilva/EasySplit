<?php

namespace App\Http\Requests;

use App\Trait\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExpenseRequestUpdate extends ExpenseRequest
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
        $rulesExpenseCreate = parent::rules();
        unset($rulesExpenseCreate['payee_id']);
        $rulesExpenseUpdate = [
            'id' => ['required', 'integer']
        ];

        return array_merge($rulesExpenseCreate, $rulesExpenseUpdate);
    }

    public function messages(): array {
        $messagesExpenseCreate = parent::messages();
        unset($messagesExpenseCreate['payee_id']);
        $messagesExpenseUpdate = [
            'id.required' => 'O id é obrigatório',
            'id.interger' => 'O id deve ser um número'
        ];

        return array_merge($messagesExpenseCreate, $messagesExpenseUpdate);
    }
}
