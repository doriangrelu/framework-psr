<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 29/11/17
 * Time: 14:30
 */

namespace Framework\Router;


use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

trait RouterAware
{
    public function redirect(string $path, ?array $params = [], ?array $queryParams=[]): ResponseInterface
    {
        $redirectUri = $this->generateUri($path, $params, $queryParams);
        return (new Response())
            ->withStatus(301)
            ->withHeader('Location', $redirectUri);
    }
}