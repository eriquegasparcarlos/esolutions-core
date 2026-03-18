<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('apiResponse')) {
    /**
     * Devuelve una respuesta JSON estandarizada para la API.
     *
     * @param int $code
     * @param mixed $data
     * @return JsonResponse
     */
    function apiResponse(array $data, int $code = 200): JsonResponse
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
    function apiSuccess(string $message, $code = 200, $data = null): JsonResponse
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
    function apiError(string $message, $code = 500, $errors = null): JsonResponse
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

if (!function_exists('remove_new_line')) {
    function remove_new_line($text): ?string
    {
        if (is_null($text)) return null;
        return preg_replace('/[\r\n]+/', ' | ', trim($text));
    }
}

if (!function_exists('str_to_upper_utf8')) {
    function str_to_upper_utf8($text): string
    {
        return mb_strtoupper($text, 'utf-8');
    }
}

if (!function_exists('funcStringRandom')) {
    /**
     * Genera una cadena aleatoria segura de longitud $length usando caracteres definidos.
     *
     * @param int $length
     * @return string
     * @throws \Random\RandomException
     */
    function funcStringRandom(int $length): string
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
    function funcRemoveSpaces($text): string
    {
        return preg_replace(['/\s+/', '/^\s|\s$/'], [' ', ''], $text);
    }
}

if (!function_exists('funcStrToUpper')) {
    function funcStrToUpper($text): string
    {
        return mb_strtoupper($text, 'utf-8');
    }
}

if (!function_exists('funcStrToLower')) {
    function funcStrToLower($text): string
    {
        return mb_strtolower($text, 'utf-8');
    }
}

if (!function_exists('funcGetDomain')) {
    function funcGetDomain(): string
    {
        $host = parse_url(request()->root(), PHP_URL_HOST);
        return $host ?: '';
    }
}

if (!function_exists('funcNumberToLetters')) {
    function funcNumberToLetters(): string
    {
        return request()->url();
    }
}

if (!function_exists('funcIsWindows')) {
    function funcIsWindows(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}

if (!function_exists('funcNumberFormatXml')) {
    function funcNumberFormatXml($value, $decimals = 2): string
    {
        return number_format($value, $decimals, '.', '');
    }
}

if (!function_exists('helper_get_item_image_url')) {
    function helper_get_item_image_url($image_name, $folder)
    {
        if (!is_null($image_name) && $image_name !== '') {
            $tenant_id = tenant()?->id;
            return asset('storage/uploads/' . $tenant_id . '/' . $folder . '/' . $image_name);
        }
        return null;
    }
}

if (!function_exists('helper_get_item_name')) {
    function helper_get_item_name($item)
    {
        $item_name = $item->name;
        $parent_name = optional($item->parent)->name;
        if ($parent_name) {
            $item_name = $parent_name . ' / ' . $item_name;
        }
        return $item_name;
    }
}

if (!function_exists('helper_options_establishments_by_user_with_all')) {
    /**
     * Obtiene las opciones de establecimientos filtradas por usuario
     * Nota: El componente Filter agrega automáticamente la opción "Todos" con includeAllOption()
     *
     * @return array
     */
    function helper_options_establishments_by_user_with_all(): array
    {
        $filterHelper = new \App\Helpers\FilterHelper();
        $establishments = $filterHelper->establishmentsByAuth();

        $options = [];

        foreach ($establishments as $establishment) {
            $options[] = [
                'id' => $establishment->id,
                'name' => $establishment->name
            ];
        }

        return $options;
    }
}

if (!function_exists('helper_options_inventory_operations_with_all')) {
    function helper_options_inventory_operations_with_all(): array
    {
        return \Modules\Inventory\Models\InventoryOperation::query()
            ->get(['id', 'name'])
            ->map(fn($row) => ['id' => $row->id, 'name' => $row->name])
            ->toArray();
    }
}

if (!function_exists('get_url_logo')) {
    function get_url_logo($logo): ?string
    {
        if (!is_null($logo) && $logo !== '') {
            $tenant_id = tenant()?->id;
            return asset('storage/uploads/' . $tenant_id . '/logos/' . $logo);
        }
        return null;
    }
}

if (!function_exists('get_public_path_logo')) {
    function get_public_path_logo($logo): ?string
    {
        if (!is_null($logo) && $logo !== '') {
            $tenant_id = tenant()?->id;
            return public_path('storage/uploads/' . $tenant_id . '/logos/' . $logo);
        }
        return null;
    }
}

if (!function_exists('get_url_logo_tenant')) {
    function get_url_logo_tenant($logo): ?string
    {
        if (!is_null($logo) && $logo !== '') {
            $tenant_id = tenant()?->id;
            return asset('storage/uploads/' . $tenant_id . '/establishments/' . $logo);
        }
        return null;
    }
}

if (!function_exists('get_public_path_logo_tenant')) {
    function get_public_path_logo_tenant($logo): ?string
    {
        if (!is_null($logo) && $logo !== '') {
            $tenant_id = tenant()?->id;
            return public_path('storage/uploads/' . $tenant_id . '/establishments/' . $logo);
        }
        return null;
    }
}

if (!function_exists('is_windows')) {
    function is_windows(): bool
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
}

if (!function_exists('qr_generate')) {
    function qr_generate(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        $size = 200;
        $image = imagecreate($size, $size);
        imagecolorallocate($image, 255, 255, 255);

        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);

        return base64_encode($png);
    }
}

if (!function_exists('number_format_decimal')) {
    function number_format_decimal($value, $decimals = 2): string
    {
        return number_format((float)$value, $decimals, '.', '');
    }
}

if (!function_exists('number_format_value_price_unit_decimal')) {
    function number_format_value_price_unit_decimal($value, $decimals = 6): string
    {
        return number_format((float)$value, $decimals, '.', '');
    }
}

if (!function_exists('get_url_pdf')) {
    function get_url_pdf(string $table, $record, string $format = 'a4'): ?string
    {
        $root = ($record->soap_type_id === '01') ? 'demo' : 'production';
        $path = $root . '/' . $table . '/pdf_' . $format . '/' . $record->filename . '.pdf';

        if (\Illuminate\Support\Facades\Storage::exists($path)) {
            return \Illuminate\Support\Facades\Storage::url($path);
        }
        return null;
    }
}
