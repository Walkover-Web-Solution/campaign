<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request, $hasError = false)
    {
        $status = "success";
        if($hasError){
            $status = "error";
        }
        return [
            'data'=> parent::toArray($request),
            'status'=>$status,
            'hasError'=>$hasError,
            'errors'=>[]
        ];        
    }
}
