<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('apiResponse')) {
    /**
     * Devuelve una respuesta JSON estandarizada para la API.
     *
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    function apiResponse(array $data, $code = 200)
    {
        return response()->json($data, $code,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('apiSuccess')) {
    /**
     * Devuelve una respuesta JSON estandarizada de éxito para la API.
     *
     * @param string $message
     * @param int $code
     * @param mixed $data
     * @return JsonResponse
     */
    function apiSuccess($message, $code = 200, $data = null)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];
        if (!is_null($data)) {
            $response['data'] = $data;
        }
        return response()->json($response, $code,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('apiError')) {
    /**
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return JsonResponse
     */
    function apiError($message, $code = 500, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];
        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $code,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('funcStringRandom')) {
    /**
     * Genera una cadena aleatoria segura de longitud $length usando caracteres definidos.
     *
     * @param int $length
     * @return string
     */
    function funcStringRandom($length)
    {
        $characters = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}

if (!function_exists('funcRemoveSpaces')) {
    /**
     * @param string $text
     * @return string
     */
    function funcRemoveSpaces($text)
    {
        return preg_replace(['/\s+/', '/^\s|\s$/'], [' ', ''], $text);
    }
}

if (!function_exists('funcStrToUpper')) {
    /**
     * @param string $text
     * @return string
     */
    function funcStrToUpper($text)
    {
        return mb_strtoupper($text, 'utf-8');
    }
}

if (!function_exists('funcStrToLower')) {
    /**
     * @param string $text
     * @return string
     */
    function funcStrToLower($text)
    {
        return mb_strtolower($text, 'utf-8');
    }
}

if (!function_exists('funcGetDomain')) {
    /**
     * @return string
     */
    function funcGetDomain()
    {
        $host = parse_url(request()->root(), PHP_URL_HOST);
        return $host ?: '';
    }
}

if (!function_exists('funcNumberToLetters')) {
    /**
     * @return string
     */
    function funcNumberToLetters()
    {
        return request()->url();
    }
}

if (!function_exists('funcIsWindows')) {
    /**
     * @return bool
     */
    function funcIsWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}

if (!function_exists('funcNumberFormatXml')) {
    /**
     * @param mixed $value
     * @param int $decimals
     * @return string
     */
    function funcNumberFormatXml($value, $decimals = 2)
    {
        return number_format($value, $decimals, '.', '');
    }
}
