<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CacheHeaders
{
    /**
     * Public listing pages that are safe to cache briefly at the edge.
     *
     * File delivery routes are deliberately excluded: they stream from the
     * private disk and must not be cached by shared proxies.
     *
     * @var list<string>
     */
    private const CACHEABLE_PATHS = [
        'kegiatan',
        'kegiatan/*',
        'dosen',
        'dokumen',
        'alumni',
        'profil',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (! $this->isCacheable($request, $response)) {
            return $response;
        }

        $response->headers->set('Cache-Control', 'public, max-age=60, stale-while-revalidate=300');

        $content = $response->getContent();

        if (is_string($content) && $content !== '') {
            $response->setEtag(md5($content));
        }

        return $response;
    }

    /**
     * A response is cacheable only for GET/HEAD listing pages that carry a
     * fully buffered body. Streamed responses expose no content to hash, so
     * hashing them would emit an identical ETag for every resource.
     */
    private function isCacheable(Request $request, Response $response): bool
    {
        return $request->isMethodCacheable()
            && $response->isSuccessful()
            && ! $response instanceof StreamedResponse
            && $request->is(...self::CACHEABLE_PATHS);
    }
}
