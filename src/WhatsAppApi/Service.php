<?php

namespace App\ESolutions\WhatsAppApi;

use App\Models\System\Configuration;
use GuzzleHttp\Client;
use Throwable;

class Service
{
    const BASE_URL = 'https://ws.apiperu.dev';

    /**
     * Enviar PDF por WhatsApp.
     * Usa el endpoint /message/send/pdf (auto-selecciona la primera sesión conectada).
     *
     * @param string $base64Pdf  Contenido del PDF codificado en base64
     * @param string $number     Número de teléfono con código de país (ej: 51999999999)
     * @param string $message    Mensaje que acompaña al PDF
     * @param string $filename   Nombre del archivo (ej: factura-001.pdf)
     * @return array
     */
    public static function sendPdf($base64Pdf, $number, $message = '', $filename = 'document.pdf')
    {
        try {
            $config = self::getConfig();

            $client = new Client([
                'verify' => false,
                'connect_timeout' => 5,
                'timeout' => 30,
            ]);

            $response = $client->post($config['url'] . '/message/send/pdf', [
                'headers' => self::buildHeaders($config['token']),
                'json' => [
                    'file' => $base64Pdf,
                    'number' => $number,
                    'message' => $message,
                    'filename' => $filename,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Enviar mensaje de texto.
     *
     * @param string $sessionId
     * @param string $to       Número con código de país
     * @param string $text
     * @return array
     */
    public static function sendText($sessionId, $to, $text)
    {
        try {
            $config = self::getConfig();

            $client = new Client([
                'verify' => false,
                'connect_timeout' => 5,
                'timeout' => 15,
            ]);

            $response = $client->post($config['url'] . '/messages/send/text', [
                'headers' => self::buildHeaders($config['token']),
                'json' => [
                    'sessionId' => $sessionId,
                    'to' => $to,
                    'text' => $text,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Listar sesiones.
     *
     * @return array
     */
    public static function getSessions()
    {
        try {
            $config = self::getConfig();

            $client = new Client([
                'verify' => false,
                'connect_timeout' => 5,
                'timeout' => 10,
            ]);

            $response = $client->get($config['url'] . '/sessions', [
                'headers' => self::buildHeaders($config['token']),
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Estado de una sesión.
     *
     * @param string $sessionId
     * @return array
     */
    public static function getSessionStatus($sessionId)
    {
        try {
            $config = self::getConfig();

            $client = new Client([
                'verify' => false,
                'connect_timeout' => 5,
                'timeout' => 10,
            ]);

            $response = $client->get($config['url'] . '/sessions/' . $sessionId . '/status', [
                'headers' => self::buildHeaders($config['token']),
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Estado de entrega de un mensaje.
     *
     * @param string $messageId
     * @return array
     */
    public static function getMessageStatus($messageId)
    {
        try {
            $config = self::getConfig();

            $client = new Client([
                'verify' => false,
                'connect_timeout' => 5,
                'timeout' => 10,
            ]);

            $response = $client->get($config['url'] . '/messages/' . $messageId . '/status', [
                'headers' => self::buildHeaders($config['token']),
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    // ===== Helpers =====

    /**
     * Obtener URL y token desde la configuración system.
     *
     * @return array ['url' => string, 'token' => string]
     * @throws \RuntimeException
     */
    private static function getConfig()
    {
        $config = Configuration::query()
            ->select('ws_api_token')
            ->first();

        $token = $config->ws_api_token ?? '';

        if ($token === '') {
            throw new \RuntimeException('El token de integración WhatsApp no está configurado.');
        }

        return [
            'url' => self::BASE_URL,
            'token' => $token,
        ];
    }

    /**
     * @param string $token
     * @return array
     */
    private static function buildHeaders($token)
    {
        return [
            'x-api-key' => $token,
            'x-app-version' => config('version.version', ''),
            'x-app-build' => config('version.build', ''),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}
