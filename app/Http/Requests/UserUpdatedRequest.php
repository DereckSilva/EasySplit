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
            $index = array_search("required", $rule);
            unset($rule[$index]);

            if ($key == 'email') {
                $rule[] = "required_without:id";
            }
            return $rule;
        })->toArray();
        unset($user['password']);

        $updated = [
            'id' => ['required_without:email', 'integer', 'exists:users,id'],
        ];

        return array_merge($user, $updated);
    }

    public function messages(): array {
        $user = collect(parent::messages())->filter(function ($rule, $key) {
            return !Str::contains($key, 'required');
        })->toArray();
        $user = array_merge($user, ["email.required_without" => "O e-mail é obrigatório quando o id não é informado"]);

        $updated = [
            'id.required_without' => 'O campo id é obrigatório quando o e-mail não é informado',
            'id.integer'  => 'O campo id deve ser um número inteiro',
            'id.exists'   => 'O usuário não existe'
        ];
        unset($user['password']);


        return array_merge($user, $updated);
    }
}
