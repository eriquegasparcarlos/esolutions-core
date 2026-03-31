<?php

use Illuminate\Http\Request;

if (!function_exists('logUserException')) {
    /**
     * Registra un error en un archivo de log personalizado por usuario.
     *
     * @param Throwable $e
     * @param Request|null $request
     * @return void
     */
    function logUserException(Throwable $e, Request $request = null)
    {
        $request = $request ?: request();
        $user = auth()->user();
        $userId = $user ? $user->id : 'guest';

        // IP real intentando respetar proxies/CDN si TrustProxies está bien configurado
        $clientIp = $request->ip();
        $xff = $request->header('X-Forwarded-For');
        $forwardedIps = $xff ? array_map('trim', explode(',', $xff)) : [];

        // En entornos con Cloudflare / Nginx pueden venir estos headers
        $realIp = $request->header('CF-Connecting-IP');
        if ($realIp === null) {
            $realIp = $request->header('X-Real-IP');
        }
        if ($realIp === null) {
            $realIp = isset($forwardedIps[0]) ? $forwardedIps[0] : $clientIp;
        }

        // Dominio/host que usó el cliente para llegar al API
        $host = $request->getHost();
        $httpHost = $request->getSchemeAndHttpHost();

        $logEntry = [
            'timestamp' => now()->toDateTimeString(),
            'message'   => $e->getMessage(),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'url'       => $request->fullUrl(),
            'method'    => $request->method(),
            'input'     => $request->all(),
            'user_id'   => $userId,
            'user_email'=> $user ? $user->email : null,
            'ip'         => $realIp,
            'ip_chain'   => $forwardedIps,
            'user_agent' => $request->userAgent(),
            'host'       => $host,
            'http_host'  => $httpHost,
            'origin'     => $request->header('Origin'),
            'referer'    => $request->header('Referer'),
        ];

        $filename = storage_path("logs/user-{$userId}.log");
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($filename, json_encode($logEntry) . PHP_EOL, FILE_APPEND);
    }
}
