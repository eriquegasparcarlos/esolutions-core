<?php

namespace App\ESolutions\ApiPeruDev;

use App\Models\System\Configuration;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Throwable;

class Service
{
    const BASE_URL = 'https://apiperu.dev';

    /**
     * @param Request $request
     * @return mixed
     */
    public static function searchRuc(Request $request)
    {
        return self::searchWithInput('ruc', $request->input('number'));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public static function searchDni(Request $request)
    {
        return self::searchWithInput('dni', $request->input('number'));
    }

    /**
     * @param string $type
     * @param string $number
     * @return mixed
     */
    public static function searchWithInput($type, $number)
    {
        try {
            $client = new Client([
                'verify' => false,
                'connect_timeout' => 3,
                'timeout' => 5,
            ]);

            $response = $client->post(self::BASE_URL . "/api/$type", [
                'headers' => self::buildHeaders(),
                'json' => [
                    $type => $number,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public static function searchExchangeRateSale(Request $request)
    {
        return self::searchExchangeRateSaleWithInput($request->input('date'));
    }

    /**
     * Consulta CPE (Comprobante de Pago Electrónico) en SUNAT.
     *
     * @param Request $request
     * @return mixed
     */
    public static function searchCpe(Request $request)
    {
        return self::searchCpeWithInput(
            $request->input('ruc_emisor'),
            $request->input('codigo_tipo_documento'),
            $request->input('serie_documento'),
            $request->input('numero_documento'),
            $request->input('fecha_de_emision'),
            $request->input('total')
        );
    }

    /**
     * @param string $rucEmisor
     * @param string $codigoTipoDocumento
     * @param string $serieDocumento
     * @param string $numeroDocumento
     * @param string $fechaDeEmision  formato yyyy-mm-dd
     * @param string|float $total
     * @return mixed
     */
    public static function searchCpeWithInput($rucEmisor, $codigoTipoDocumento, $serieDocumento, $numeroDocumento, $fechaDeEmision, $total)
    {
        try {
            $client = new Client([
                'verify' => false,
                'connect_timeout' => 3,
                'timeout' => 10,
            ]);

            $response = $client->post(self::BASE_URL . '/api/cpe', [
                'headers' => self::buildHeaders(),
                'json' => [
                    'ruc_emisor' => $rucEmisor,
                    'codigo_tipo_documento' => $codigoTipoDocumento,
                    'serie_documento' => $serieDocumento,
                    'numero_documento' => $numeroDocumento,
                    'fecha_de_emision' => $fechaDeEmision,
                    'total' => $total,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Consulta CPE múltiple v2 contra SUNAT.
     * Máximo 100 comprobantes por lote.
     *
     * @param array $comprobantes Array de strings con formato "RUC|TIPO_DOC|SERIE|NUMERO|FECHA|TOTAL"
     * @param string $rucEmpresa RUC de la empresa (opcional)
     * @param string $solUsuario Usuario SOL (opcional)
     * @param string $claveUsuario Clave SOL (opcional)
     * @return array
     */
    public static function searchCpeMultiple(array $comprobantes, $rucEmpresa = '', $solUsuario = '', $claveUsuario = '')
    {
        try {
            $client = new Client([
                'verify' => false,
                'connect_timeout' => 5,
                'timeout' => 60,
            ]);

            $response = $client->post(self::BASE_URL . '/api/validacion-multiple-cpe-v2', [
                'headers' => self::buildHeaders(),
                'json' => [
                    'ruc_empresa' => $rucEmpresa,
                    'sol_usuario' => $solUsuario,
                    'clave_usuario' => $claveUsuario,
                    'comprobantes' => $comprobantes,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Helper: construye el string de comprobante para searchCpeMultiple.
     *
     * @param string $ruc
     * @param string $tipoDoc
     * @param string $serie
     * @param string|int $numero
     * @param string $fecha formato yyyy-mm-dd
     * @param string|float $total
     * @return string "RUC|TIPO_DOC|SERIE|NUMERO|FECHA|TOTAL"
     */
    public static function buildCpeString($ruc, $tipoDoc, $serie, $numero, $fecha, $total)
    {
        return implode('|', [$ruc, $tipoDoc, $serie, $numero, $fecha, $total]);
    }

    /**
     * Consulta establecimientos anexos de un RUC en SUNAT.
     *
     * @param string $number RUC number
     * @return array
     */
    public static function searchEstablishments($number)
    {
        try {
            $client = new Client([
                'verify' => false,
                'connect_timeout' => 3,
                'timeout' => 5,
            ]);

            $response = $client->post(self::BASE_URL . '/api/ruc_establecimientos_anexos', [
                'headers' => self::buildHeaders(),
                'json' => [
                    'ruc' => $number,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Consulta domicilio fiscal de un RUC en SUNAT.
     *
     * @param string $number RUC number
     * @return array
     */
    public static function searchFiscalAddress($number)
    {
        try {
            $client = new Client([
                'verify' => false,
                'connect_timeout' => 3,
                'timeout' => 5,
            ]);

            $response = $client->post(self::BASE_URL . '/api/ruc_domicilio_fiscal', [
                'headers' => self::buildHeaders(),
                'json' => [
                    'ruc' => $number,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Consulta listado de puertos.
     *
     * @return array
     */
    public static function searchPorts()
    {
        try {
            $client = new Client([
                'verify' => false,
                'connect_timeout' => 3,
                'timeout' => 5,
            ]);

            $response = $client->post(self::BASE_URL . '/api/puertos', [
                'headers' => self::buildHeaders(),
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Consulta listado de aeropuertos.
     *
     * @return array
     */
    public static function searchAirports()
    {
        try {
            $client = new Client([
                'verify' => false,
                'connect_timeout' => 3,
                'timeout' => 5,
            ]);

            $response = $client->post(self::BASE_URL . '/api/aeropuertos', [
                'headers' => self::buildHeaders(),
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Validación masiva de CPE v1 (comprobantes como objetos individuales).
     *
     * @param array $data Array con keys: ruc_empresa, sol_usuario, clave_usuario, comprobantes
     * @return array
     */
    public static function searchMassiveCpe(array $data)
    {
        try {
            $client = new Client([
                'verify' => false,
                'connect_timeout' => 5,
                'timeout' => 60,
            ]);

            $response = $client->post(self::BASE_URL . '/api/validacion_multiple_cpe', [
                'headers' => self::buildHeaders(),
                'json' => $data,
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param string $date
     * @return mixed
     */
    public static function searchExchangeRateSaleWithInput($date)
    {
        try {
            $client = new Client([
                'verify' => false,
                'connect_timeout' => 3,
                'timeout' => 5,
            ]);

            $response = $client->post(self::BASE_URL . '/api/tipo_de_cambio', [
                'headers' => self::buildHeaders(),
                'json' => [
                    'fecha' => $date,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // ===== Helpers =====

    /**
     * Obtener token desde la configuración system.
     *
     * @return string
     */
    private static function getToken()
    {
        $config = Configuration::query()->select('token_apiruc')->first();
        return $config->token_apiruc ?? '';
    }

    /**
     * @return array
     */
    private static function buildHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . self::getToken(),
            'x-app-version' => config('version.version', ''),
            'x-app-build'   => config('version.build', ''),
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];
    }
}
