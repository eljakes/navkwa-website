<?php

namespace App\Http\Middleware;

use App\Models\WebsiteVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackWebsiteVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if ($this->shouldTrack($request, $response)) {
            WebsiteVisit::create([
                'path' => '/'.ltrim($request->path(), '/'),
                'route_name' => $request->route()?->getName(),
                'method' => $request->method(),
                'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                'ip_address' => $request->ip(),
                'country' => $request->header('CF-IPCountry') ?: $request->header('X-Country-Code'),
                'user_agent' => $request->userAgent(),
                'referer' => $request->headers->get('referer'),
                'visited_at' => now(),
            ]);
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        return $request->isMethod('GET')
            && $response->isSuccessful()
            && ! $request->expectsJson()
            && ! $request->is('admin*')
            && ! $request->is('chat*')
            && ! $request->is('contact*')
            && ! $request->is('up')
            && ! $request->is('storage*')
            && ! $request->is('assets*');
    }
}
