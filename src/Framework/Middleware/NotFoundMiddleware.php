<?php
namespace Framework\Middleware;

use App\Bundle\Errors\Controller\Errors;
use Framework\App;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundMiddleware
{

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $container=$request->getAttribute("container");
        $controller= new Errors();
        $controller->initialize($request,$container);
        return new Response(404, [], $controller->notFound($request));
    }
}
