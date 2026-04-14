<?php

namespace App\ESolutions\ApiPeruDev;

use App\ESolutions\Utils\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class Service extends Controller
{
    public function searchRuc(Request $request)
    {
        try {
            $response = self::baseRequest()
                ->post(config('configuration.api_url') . '/ruc', [
                    'ruc' => $request->input('number'),
                ]);

            return $response->json();

        } catch (Throwable $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    public function searchDni(Request $request)
    {
        try {
            $response = self::baseRequest()
                ->post(config('configuration.api_url') . '/dni', [
                    'dni' => $request->input('number'),
                ]);

            return $response->json();

        } catch (Throwable $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    public static function searchWithInput(string $type, string $number): array
    {
        try {
            $param = $type === 'ruc' ? 'ruc' : 'dni';

            $response = self::baseRequest()
                ->post(config('configuration.api_url') . '/' . $type, [
                    $param => $number,
                ]);

            return $response->json();

        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public static function searchExchangeRateSaleWithInput(string $date): array
    {
        try {
            $response = self::baseRequest()
                ->post(config('configuration.api_url') . '/tipo-de-cambio', [
                    'fecha' => $date,
                ]);

            return $response->json();

        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    private static function baseRequest(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withOptions(['verify' => false])
            ->withToken(config('configuration.api_token'))
            ->withHeaders([
                'x-app-version' => config('version.version', ''),
                'x-app-build' => config('version.build', ''),
            ])
            ->connectTimeout(5)
            ->timeout(10);
    }
}
