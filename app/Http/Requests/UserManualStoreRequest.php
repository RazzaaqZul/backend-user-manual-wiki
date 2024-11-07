<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserManualStoreRequest extends FormRequest
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
     */
    public function rules()
    {
        return [
            // Validasi
            "title" => ["required", "unique:user_manuals,title", "max:100"],
            "img" => ["required"],
            "short_desc" => ["required", "max:200"],
            // "initial_editor" => ["required", "max:100"], // Sesuaikan dengan kolom "creator" yang Anda gunakan
            // "latest_editor" => ["required", "max:100"],
            "version" => ["required"],  
            "content" => ["required"],
            "category" => ["required", "in:internal,eksternal"],
            "size" => ["required"],
            'user_id' => ['required', 'exists:users,user_id'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // parameternya adalah response dan status code
        throw new HttpResponseException(response([
            "message" => "Failed to create a new User Manual",
            "errors" => $validator->getMessageBag()
        ],400));
    }

    
}
