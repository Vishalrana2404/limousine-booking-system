<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AjaxResource extends JsonResource
{
    protected $statusCode = 200;
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
            'message' => (in_array($this->statusCode, [200, 201])) ? 'success' : 'error',
            'data' => $this->resource,
            'status' => (in_array($this->statusCode, [200, 201])) ? 'OK' : 'NOK',

        ];
    }

    public function with($request)
    {
        return [
            'version' => '1.0',
            'author' => 'zapbuild',
        ];
    }
}
