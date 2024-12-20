<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Validasi
            "email" => ['required', 'email', 'max:100'],
            "password" => ["required","max:200"],
        ];
    }
    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email tidak boleh lebih dari :max karakter.',

            'password.required' => 'Kata sandi wajib diisi.',
            'password.max' => 'Kata sandi tidak boleh lebih dari :max karakter.',
        ];
    }

    // Jika terjadi error akan throw pada form request akan dilempar ke ValidationException
    // kita mau dalam bentuk HTTP Response misalnya
    protected function failedValidation(Validator $validator)
    {
        // parameternya adalah response dan status code
        throw new HttpResponseException(response([
            // getMessageBag() otomatis akan di set error key valuenya
            "errors" => $validator->getMessageBag()
        ],400));
    }
}
