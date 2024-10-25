<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */public function rules()
{
    return [
        "username" => [
            "nullable",
            "max:100",
            Rule::unique('users', 'username')->ignore($this->route('id'), 'user_id') // Abaikan user_id yang sedang di-update
        ],
        "email" => [
            "nullable",
            "email",
            Rule::unique('users', 'email')->ignore($this->route('id'), 'user_id') // Abaikan user_id yang sedang di-update
        ],
        "name" => ["nullable", "max:255"],
        "role" => ["nullable", "in:technical_writer,admin"],
    ];
}

    protected function failedValidation(Validator $validator)
    {
        // parameternya adalah response dan status code
        throw new HttpResponseException(response([
            "message" => 'Failed to update a user',
            "errors" => $validator->getMessageBag()
        ],400));
    }
}
