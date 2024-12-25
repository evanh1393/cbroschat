<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        // Proceed with the request and get the response
        $response = $next($request);

        // Define your Content Security Policy as a single line
        $csp = "default-src 'self'; script-src 'self' https://js.pusher.com; style-src 'self' 'unsafe-inline'; connect-src 'self' https://api.pusherapp.com";

        // Set the Content-Security-Policy header
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
