<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserManualResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */public function toArray($request)
        {
            return [
                "user_manual_id" => $this->when(isset($this->user_manual_id), $this->user_manual_id),
                "user_manual_history_id" => $this->when(isset($this->user_manual_history_id), $this->user_manual_history_id),
                "user_id" => $this->user_id,
                "title" => $this->title,
                "img" => $this->img,
                "short_desc" => $this->short_desc,
                "initial_editor" => $this->initial_editor,
                "latest_editor" => $this->latest_editor,
                "version" => $this->version,
                "content" => $this->content,
                "category" => $this->category,
                "size" => $this->size,
                "created_at" => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
                "updated_at" => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
                "deleted_at" => $this->deleted_at ? $this->deleted_at->format('Y-m-d H:i:s') : null,
            ];
        }

}
