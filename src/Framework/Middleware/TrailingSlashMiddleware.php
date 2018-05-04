<?php
namespace Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class TrailingSlashMiddleware
{

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $uri = $request->getUri()->getPath();
        if (WEB_ROOT != "/") {
            $baseuri = str_replace(WEB_ROOT, "", $uri);
        } else {
            $baseuri = WEB_ROOT;
        }
        $response = new Response();
        if (!empty($baseuri) && strlen($uri) > 1 && $uri[strlen($uri) - 1] === "/") {
            $response = $response->withStatus(301);
            $response = $response->withHeader('Location', substr($uri, 0, strlen($uri) - 1));
            return $response;
        }
        return $next($request);
    }
}
