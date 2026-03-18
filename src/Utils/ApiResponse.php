<?php

namespace App\ESolutions\Utils;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    const array HEADERS = ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'];
    const int FLAGS = JSON_UNESCAPED_UNICODE;

    public static function success(string $message, int $code = 200, array $data = null): JsonResponse
    {
        return self::response([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error(string $message, int $code = 500, array $errors = null): JsonResponse
    {
        return self::response([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    public static function response(array $data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code, self::HEADERS, self::FLAGS);
    }
}
