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
    function logUserException(Throwable $e, ?Request $request = null): void
    {
        $request = $request ?: request();
        $user = auth()->user();
        $userId = $user?->id ?? 'guest';

        // IP real intentando respetar proxies/CDN si TrustProxies está bien configurado
        $clientIp = $request->ip(); // respeta Trusted Proxies
        $xff = $request->header('X-Forwarded-For');
        $forwardedIps = $xff ? array_map('trim', explode(',', $xff)) : [];

        // En entornos con Cloudflare / Nginx pueden venir estos headers
        $realIp = $request->header('CF-Connecting-IP')
            ?? $request->header('X-Real-IP')
            ?? ($forwardedIps[0] ?? $clientIp);

        // Dominio/host que usó el cliente para llegar al API
        $host = $request->getHost();                // ejemplo: api.tu-dominio.com
        $httpHost = $request->getSchemeAndHttpHost(); // ejemplo: https://api.tu-dominio.com

        $logEntry = [
            'timestamp' => now()->toDateTimeString(),
            'message'   => $e->getMessage(),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'url'       => $request->fullUrl(),
            'method'    => $request->method(),
            'input'     => $request->all(),
            'user_id'   => $userId,
            'user_email'=> $user?->email,
            'ip'         => $realIp,
            'ip_chain'   => $forwardedIps,           // toda la cadena de proxies (si existe)
            'user_agent' => $request->userAgent(),
            'host'       => $host,
            'http_host'  => $httpHost,
            'origin'     => $request->header('Origin'),
            'referer'    => $request->header('Referer'),
        ];

        $filename = storage_path("logs/user-{$userId}.log");
        File::ensureDirectoryExists(dirname($filename));
        file_put_contents($filename, json_encode($logEntry) . PHP_EOL, FILE_APPEND);
    }
}
