<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserManualCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
         // Iterasi setiap item dalam collection
         return $this->collection->map(function ($userManual) {
            return [
                // Conditionally include "user_manual_id" if it exists
                "user_manual_id" => $this->when(isset($userManual->user_manual_id), $userManual->user_manual_id),
        
                // Conditionally include "user_manual_history_id" if it exists
                "user_manual_history_id" => $this->when(isset($userManual->user_manual_history_id), $userManual->user_manual_history_id),
        
                "title" => $userManual->title,
                "img" => $userManual->img,
                "short_desc" => $userManual->short_desc,
                "initial_editor" => $userManual->initial_editor, // Sesuaikan dengan kolom "creator" yang digunakan
                "latest_editor" => $userManual->latest_editor, // Sesuaikan dengan kolom "creator" yang digunakan
                "version" => $userManual->version,
                "content" => $userManual->content,
                "category" => $userManual->category,
                "size" => $userManual->size,
                "user_id" => $userManual->user_id,
                "update_desc" => $userManual->update_desc,
                "created_at" => $userManual->created_at->format('Y-m-d H:i:s'),
                "updated_at" => $userManual->updated_at->format('Y-m-d H:i:s'),
            ];
        });
    }
}
