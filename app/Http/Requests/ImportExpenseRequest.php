<?php

namespace App\Http\Requests;

use App\Trait\ResponseHttp;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ImportExpenseRequest extends FormRequest
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
            'expenseCSV' => ['required', 'mimes:csv,txt']
        ];
    }

    public function messages(): array {
        return [
            'expenseCSV.required' => "O arquivo CSV é obrigatório com o nome 'expenseCSV'",
            'expenseCSV.mimes' => 'O arquivo deve ser do tipo CSVsws'
        ];
    }


    public function failedValidation(Validator $validator): HttpResponseException {
        return $this->retornoExceptionErroRequest(false, 'Erro de validação', 400, $validator->errors());
    }
}
