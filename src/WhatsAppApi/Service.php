<?php

namespace App\ESolutions\WhatsAppApi;

use Illuminate\Support\Facades\Http;
use Throwable;

class Service
{
    /**
     * Enviar PDF por WhatsApp.
     * Usa el endpoint /message/send/pdf (auto-selecciona la primera sesión conectada).
     */
    public static function sendPdf(string $base64Pdf, string $number, string $message = '', string $filename = 'document.pdf'): array
    {
        try {
            $response = self::http(30)
                ->post(self::url('/message/send/pdf'), [
                    'file' => $base64Pdf,
                    'number' => $number,
                    'message' => $message,
                    'filename' => $filename,
                ]);

            return $response->json();

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Enviar mensaje de texto.
     */
    public static function sendText(string $sessionId, string $to, string $text): array
    {
        try {
            $response = self::http()
                ->post(self::url('/messages/send/text'), [
                    'sessionId' => $sessionId,
                    'to' => $to,
                    'text' => $text,
                ]);

            return $response->json();

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Listar sesiones.
     */
    public static function getSessions(): array
    {
        try {
            $response = self::http()
                ->get(self::url('/sessions'));

            return $response->json();

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Estado de una sesión.
     */
    public static function getSessionStatus(string $sessionId): array
    {
        try {
            $response = self::http()
                ->get(self::url('/sessions/' . $sessionId . '/status'));

            return $response->json();

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Estado de entrega de un mensaje.
     */
    public static function getMessageStatus(string $messageId): array
    {
        try {
            $response = self::http()
                ->get(self::url('/messages/' . $messageId . '/status'));

            return $response->json();

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    // ===== Helpers =====

    private static function http(int $timeout = 15): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withOptions(['verify' => false])
            ->withHeaders([
                'x-api-key' => config('configuration.ws_api_token'),
                'x-app-version' => config('version.version', ''),
                'x-app-build' => config('version.build', ''),
            ])
            ->connectTimeout(5)
            ->timeout($timeout);
    }

    private static function url(string $path): string
    {
        return rtrim(config('configuration.ws_api_url', ''), '/') . $path;
    }
}
