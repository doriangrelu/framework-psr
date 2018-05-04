<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 30/11/17
 * Time: 16:03
 */

namespace Framework\Utility\Objects;


use Psr\Http\Message\ServerRequestInterface;

class Get
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Get constructor.
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function get(string $key):?string
    {
        if($this->exist($key)){
            return $this->request->getQueryParams()[$key];
        }
        return null;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exist(string $key): bool
    {
        return isset($this->request->getQueryParams()["$key"]);
    }


}