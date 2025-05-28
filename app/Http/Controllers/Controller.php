<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\AjaxResource;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /* return json response */

    public function handleResponse($data = [], $message = '', $status_code = Response::HTTP_OK, $error = [])
    {

        if ($status_code == Response::HTTP_OK) {
            return $response = response()->json([
                'status' => [
                    'code' => $status_code,
                    'message' => $message ?? 'Ok',
                ],
                'data' => $data ?? [],
                'version' => '1.0',
                'author' => 'zapbuild',
            ]);
        } else {
            return $response = response()->json([
                'status' => [
                    'code' => $status_code,
                    'message' => $message ?? 'Error',
                ],
                'error' => $error ?? [],
                'version' => '1.0',
                'author' => 'zapbuild',
            ]);
        }
    }

    public function validationErrors($data = [], $rules = [], $messages = [], $status_code = Response::HTTP_BAD_REQUEST)
    {
        $validate_data = Validator::make($data, $rules, $messages);

        if ($validate_data->fails()) {
            return  response()->json([
                'status' => [
                    'code' => $status_code,
                    'message' => $validate_data->errors()->first(),
                ],
                'error' => $validate_data->errors(),
                'version' => '1.0',
                'author' => 'zapbuild',
            ]);
        }

        return false;
    }

    public static function getHttpHeaders()
    {
        $userdata = Session::get('user');
        $bearerToken = $userdata['token'];
        $headers = [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
            'http_errors' => false,
        ];

        return $headers;
    }

    public function handleException($exception)
    {
        if ($exception instanceof \Throwable) {
            app(ExceptionHandler::class)->report($exception);
            Log::error($exception->getMessage(), [
                'file' => $exception->getFile(),
                'type' => 'Handled Exception',
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
        } else {
            Log::error($exception);
        }
    }

    public function getHttpData(Request $request) {
        $userIp = $request->ip();
        $userAgent = $request->header('User-Agent');
        $headers = [
            'headers' => [
                'Origin' => $userIp,
                'User-Agent' => $userAgent,
            ],
        ];

        return $headers;
    }
}
