<?php
namespace Framework\Utility\Objects;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 30/11/17
 * Time: 15:55
 */
class Post
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Post constructor.
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $key
     * @return null|string
     */
    public function get(string $key):?string
    {
        if($this->exist($key)){
            return $this->request->getParsedBody()[$key];
        }
        return null;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exist(string $key): bool
    {
        if(strlen($key)>0 && $key[0]!="_"){
            return isset($this->request->getParsedBody()[$key]);
        }
        return false;
    }

}