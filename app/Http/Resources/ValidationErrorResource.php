<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ValidationErrorResource extends JsonResource
{
    protected $statusCode = 422;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
  

    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }

   

    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->statusCode);
    }

    public function toArray(Request $request): array
    {
        
        return [
            'code' => $this->statusCode,
            'message' => 'Validation failed.',
            'data' => $this->resource,
            'version' => '1.0',
            'author' => 'zapbuild',
        ];
    }

   
}
