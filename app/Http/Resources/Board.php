<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Board extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'board_name' => $this->board_name,
            'board_start_at' => $this->board_start_at,
            'board_end_at' => $this->board_end_at,
            'board_final_date' => $this->board_final_date,
            'board_description' => $this->board_description,
            'detail' => $this->detail,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
            'deleted_at' => $this->deleted_at,
        ];
    }
}
