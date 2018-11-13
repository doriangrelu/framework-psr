<?php
namespace Framework\Middleware;

use App\Bundle\Errors\Controller\Errors;
use App\Framework\Exception\Http\NotFoundHttpException;
use Framework\App;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundMiddleware
{

    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @throws NotFoundHttpException
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        throw new NotFoundHttpException();
    }
}
