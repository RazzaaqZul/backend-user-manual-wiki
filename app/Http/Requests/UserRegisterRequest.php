<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRegisterRequest extends FormRequest
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
    public function rules()
    {
        return [
            "password" => ["required", "max:100", "regex:/^(?=.*[A-Z])(?=.*\d).+$/"], // Minimal satu huruf kapital dan satu angka
            "email" => ["required", "email", "unique:users,email"],
            "name" => ["required", "max:100"],
            "role" => ["required", "in:technical_writer,admin"]
        ];
    }
    
    public function messages()
    {
        return [
            'password.required' => 'Kata sandi wajib diisi.',
            'password.max' => 'Kata sandi tidak boleh lebih dari :max karakter.',
            'password.regex' => 'Kata sandi minimal mengandung 1 huruf kapital dan 1 angka.',
            
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan. Harap gunakan email lain.',

            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama tidak boleh lebih dari :max karakter.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // parameternya adalah response dan status code
        throw new HttpResponseException(response([
            "message" => 'Failed to register',
            "errors" => $validator->getMessageBag()
        ],400));
    }
}

