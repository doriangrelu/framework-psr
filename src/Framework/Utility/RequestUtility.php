<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 30/11/17
 * Time: 16:53
 */

namespace Framework\Utility;


use Framework\Utility\Objects\Get;
use Framework\Utility\Objects\Post;
use Psr\Http\Message\ServerRequestInterface;

trait RequestUtility
{
    /**
     * @var Get
     */
    protected $get;
    /**
     * @var Post
     */
    protected $post;

    /**
     * @var array|null|object
     */
    protected $parsedBody;

    /**
     * @var array
     */
    protected $parsedBodyParameters=[];

    /**
     * @var array
     */
    protected $queryParams=[];

    /**
     * @param ServerRequestInterface $request
     */
    public function setRequestProperties(ServerRequestInterface $request): void
    {
        $this->post = new Post($request);
        $this->get = new Get($request);
        $this->request = $request;
        $this->parsedBody = $this->getParsedBody($request);
        $this->queryParams=$request->getQueryParams();
    }

    /**
     * @return array|null|object
     */
    private function getParsedBody(ServerRequestInterface $request)
    {
        $body = $request->getParsedBody();
        foreach ($body as $key=>$value) {
            if (isset($key[0]) && $key[0]=="_") {
                $this->parsedBodyParameters[$key]=$value;
                unset($body[$key]);
            }
        }
        return $body;
    }

}