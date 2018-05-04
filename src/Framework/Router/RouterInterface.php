<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 30/11/17
 * Time: 19:25
 */

namespace Framework\Router;


use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    /**
     * @param string $path
     * @param callable $callback
     */
    public function scope(string $path, callable $callback): void;

    /**
     * @param $path
     * @param $action
     * @param $name
     * @param $method
     * @return Route
     * @throws \Exception
     */
    public function add($path, $action, $name, $method): Route;

    /**
     * @param string $name
     * @param array $params
     * @param array $queryParams
     * @return null|string
     */
    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string;

    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request):?Route;

}