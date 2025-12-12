<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de seguridad - Headers HTTP
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo en producción
        if (app()->environment('production')) {
            // Prevenir clickjacking
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            
            // Prevenir MIME sniffing
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            
            // Habilitar XSS filter del navegador
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            
            // Referrer policy
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            
            // Permissions policy
            $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
            
            // HSTS (solo si tienes SSL)
            if ($request->secure()) {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            }
        }

        return $response;
    }
}
