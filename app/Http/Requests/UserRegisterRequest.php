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
            // Validasi
            "username" => ["required", "max:100", "unique:users,username"],
            "password" => ["required", "max:100"],
            "email" => ["required", "email", "unique:users,email"], // Tambahkan validasi email
            "name" => ["required", "max:255"],
            "role" => ["required", "in:technical_writer,admin"] // Validasi untuk kolom role sebagai enum
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

