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
            "version" => ["required", "regex:/^\d+\.\d+\.\d+$/"],  // Menambahkan validasi regex untuk version
            "content" => ["required"],
            "category" => ["required", "in:internal,eksternal"],
            "size" => ["required"],
            'user_id' => ['required', 'exists:users,user_id'],
        ];
    }
    
    public function messages()
    {
        return [
            "title.required" => "Judul wajib diisi.",
            "title.unique" => "Judul ini sudah ada dalam sistem.",
            "title.max" => "Judul tidak boleh lebih dari 100 karakter.",
            "img.required" => "Gambar harus diunggah.",
            "short_desc.required" => "Deskripsi singkat wajib diisi.",
            "short_desc.max" => "Deskripsi singkat tidak boleh lebih dari 200 karakter.",
            "version.required" => "Versi wajib diisi dalam format X.Y.Z (misalnya 0.0.0).",
            "version.regex" => "Format versi harus dalam format X.Y.Z (misalnya 0.0.0).", // Pesan error untuk regex
            "content.required" => "Konten wajib diisi.",
            "category.required" => "Kategori wajib diisi.",
            "category.in" => "Kategori harus berupa 'internal' atau 'eksternal'.",
            "size.required" => "Ukuran wajib diisi.",
            "user_id.required" => "ID pengguna wajib diisi.",
            "user_id.exists" => "ID pengguna tidak ditemukan dalam sistem."
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
