<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomResource extends JsonResource
{

    public $hasError;

    public function __construct($resource, $hasError = false)
    {
        parent::__construct($resource);
        $this->hasError = $hasError;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $status = "success";
        $errors = [];
        if ($this->hasError) {
            $status = "error";
            $errors += parent::toArray($request);
        }
        return [
            'data' => parent::toArray($request),
            'status' => $status,
            'hasError' => $this->hasError,
            'errors' => $errors
        ];
    }
}
