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
        Log::warning($userManualId);
        return [
            // Validasi
            "title" => ["nullable", "max:100"],
            "img" => ["nullable"],
            "short_desc" => ["nullable", "max:200"],
            "initial_edtior" => ["nullable", "max:100"],
            // "latest_editor" => ["required", "max:100"],
            "version" => ["required", "max:100"],
            "content" => ["nullable"],
            "category" => ["nullable", "in:internal,eksternal"],
            "size" => ["nullable"],
            // 'user_id' => ['required', 'exists:users,user_id'],
            'at_least_one_field' => [
                function ($attribute, $value, $fail) {
                    if (empty($this->title) && empty($this->img) && empty($this->short_desc) && empty($this->creator) && empty($this->content) && empty($this->category) && empty($this->size)) {
                        $fail('At least one of title, img, short_desc, creator, content, category, or size must be filled.');
                    }
                }
            ],
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
