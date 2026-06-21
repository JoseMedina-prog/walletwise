<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrustNgrokProxies
{
    /**
     * Handle an incoming request.
     *
     * When the app is behind ngrok, every request arrives from 127.0.0.1,
     * but the original scheme/host is sent via X-Forwarded-* headers.
     * This middleware ensures Laravel:
     *   - trusts the proxy (no "page expired" / 419 CSRF errors)
     *   - generates correct absolute URLs
     *   - creates secure cookies
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('local') && $this->isFromNgrok($request)) {
            $request->setTrustedProxies(
                [$request->server('REMOTE_ADDR')],
                Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
                | Request::HEADER_X_FORWARDED_PREFIX
            );
        }

        return $next($request);
    }

    protected function isFromNgrok(Request $request): bool
    {
        $host = (string) $request->header('Host', '');
        $for  = (string) $request->header('X-Forwarded-For', '');

        return str_contains($host, '.ngrok')
            || str_contains($host, '.ngrok-free.app')
            || str_contains($host, '.ngrok.app')
            || str_contains($for, 'ngrok');
    }
}
