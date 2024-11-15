<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class UserManualUpdateRequest extends FormRequest
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
        $userManualId = $this->route('id');
        Log::warning(empty($this->title));
        return [
            // Validasi
            "title" => ["nullable", "max:100"],
            "img" => ["nullable"],
            "short_desc" => ["nullable", "max:200"],
            "initial_edtior" => ["nullable", "max:100"],
            // "latest_editor" => ["required", "max:100"],
            "version" => ["required", "regex:/^\d+\.\d+\.\d+$/"], 
            "update_desc" => ["required", "max:200"],
            "content" => ["required"],
            "category" => ["nullable", "in:internal,eksternal"],
            "size" => ["nullable"],
            // 'user_id' => ['required', 'exists:users,user_id'],
        ];
    }

    public function messages()
    {
        return [
            "title.max" => "Judul tidak boleh lebih dari 100 karakter.",
            "img.nullable" => "Gambar opsional, jika tidak diubah, biarkan kosong.",
            "short_desc.max" => "Deskripsi singkat tidak boleh lebih dari 200 karakter.",
            "initial_edtior.max" => "Editor awal tidak boleh lebih dari 100 karakter.",
            "version.required" => "Versi harus diisi.",
            "version.regex" => "Format versi harus dalam format X.Y.Z (misalnya 0.0.0).",
            "version.max" => "Versi tidak boleh lebih dari 100 karakter.",
            "update_desc.required" => "Deskripsi Perubahan singkat wajib diisi.",
            "update_desc.max" => "Deskripsi Perubahan tidak boleh lebih dari 200 karakter.",
            "content.nullable" => "Konten opsional, jika tidak diubah, biarkan kosong.",
            "category.in" => "Kategori harus berupa 'internal' atau 'eksternal'.",
            "content.required" => "Konten Wajib diisi.",
            "size.nullable" => "Ukuran opsional, jika tidak diubah, biarkan kosong."
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        // parameternya adalah response dan status code
        throw new HttpResponseException(response([
            "message" => "Failed to update User Manual",
            "errors" => $validator->getMessageBag()
        ],400));
    }

}
