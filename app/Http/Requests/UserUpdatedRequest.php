<?php

namespace App\Http\Requests;


use Illuminate\Support\Str;

class UserUpdatedRequest extends UserRequest
{
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
        $user = collect(parent::rules())->map(function ($rule, $key) {
            unset($rule[0]);
            return $rule;
        })->toArray();
        unset($user['password']);

        $updated = [
            'id' => ['required', 'integer', 'exists:users,id']
        ];

        return array_merge($user, $updated);
    }

    public function messages(): array {
        $user    = parent::messages();
        $updated = [
            'id.required' => 'O campo id é obrigatório',
            'id.integer'  => 'O campo id deve ser um número inteiro',
            'id.exists'   => 'O usuário não existe'
        ];
        unset($user['password']);


        return array_merge($user, $updated);
    }
}
