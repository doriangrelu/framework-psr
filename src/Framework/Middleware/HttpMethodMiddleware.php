<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 13/11/2018
 * Time: 16:59
 */

namespace Framework\Middleware;


use App\Framework\Exception\Http\HttpException;
use Psr\Http\Message\ServerRequestInterface;

class HttpMethodMiddleware
{

    private $_fieldName = '_method';

    private $_allowedMethod = [
        'GET' => false,
        'POST' => true,
        'PUT' => true,
        'DELETE' => true,
    ];

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        //$request = $request->withParsedBody([$this->_fieldName => 'DELETE']);
        $body = $request->getParsedBody();
        if (array_key_exists($this->_fieldName, $body) && in_array($body[$this->_fieldName], $this->_allowedMethod)) {
            $request = $request->withMethod($body[$this->_fieldName]);
        }

        return $next($request);
    }

    /**
     * @param string $method
     * @throws HttpException
     */
    public function allowedMethodOrFail(string $method):void
    {
        if(!isset($this->_allowedMethod[$method])){
            throw new HttpException("Bad HTTP Request Method: $method");
        }
    }

    public function getFieldName(): string
    {
        return $this->_fieldName;
    }

}