<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 29/11/17
 * Time: 12:24
 */

namespace Framework\Router;


use App\Framework\Auth\Role;
use Framework\Exception\CallableRouterException;

class Route
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var
     */
    private $action;
    /**
     * @var array
     */
    private $matches = [];
    /**
     * @var array
     */
    private $params = [];
    /**
     * @var string
     */
    private $uri;
    /**
     * @var null|string
     */
    private $method = null;

    /**
     * @var array
     */
    private $middlewares = [];

    private $roles = [];

    /**
     * @var string[]
     */
    private $scope = [];

    /**
     * Route constructor.
     * @param string $uri
     * @param $action
     * @param string $name
     * @param string $method
     * @throws CallableRouterException
     */
    public function __construct(string $uri, $action, string $name, string $method = "GET")
    {
        $this->uri = $this->trimUri($uri);
        if ($this->checkAction($action)) {
            $this->action = $action;
        }
        $this->name = $name;
        $this->method = $method;
    }

    private function checkAction($action)
    {
        if (is_callable($action)) {
            if (is_array($action)) {
                if (count($action) == 2 && method_exists($action[0], $action[1])) {
                    return true;
                }
            } else {
                return true;
            }
        }
        throw new CallableRouterException();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->matches;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->uri;
    }

    /**
     * @return mixed
     */
    public function getCallback()
    {
        return $this->action;
    }

    /**
     * Renvoi l'uri en fonction des paramètres donnés
     * @param array|null $params
     * @return string
     */
    public function getUri(?array $params = []): string
    {
        $uri = $this->uri;
        foreach ($params as $k => $v) {
            $uri = str_replace(":$k", $v, $uri);
        }
        $uri = $this->trimUri($uri);
        return ($uri == "/" ? WEB_ROOT : WEB_ROOT . $uri);
    }

    /**
     * Trim une URI avec le caractère '/' si besoin est.
     * @param string $uri
     * @return string
     */
    public function trimUri(string $uri): string
    {
        if ($uri != "/") {
            return trim($uri, "/");
        }
        return $uri;
    }

    /**
     * @param string $uri
     * @return bool
     */
    public function match(string $uri)
    {
        $result = false;
        $uri = $this->trimUri($uri);
        $path = preg_replace_callback("#:([\w]+)#", [$this, 'paramMatch'], $this->uri);
        $regex = "#^$path$#i";
        if (preg_match($regex, $uri, $matches)) {
            array_shift($matches);
            $this->matches = $matches;
            $result = true;
        }

        return $result;
    }

    /**
     * @param $match
     * @return string
     */
    private function paramMatch($match)
    {
        $regex = "([^/]+)";
        if (isset($this->params[$match[1]])) {
            $regex = '(' . $this->params[$match[1]] . ')';
        }
        return $regex;
    }

    /**
     * @param $param
     * @param $regex
     * @param bool $nullable
     * @return $this
     */
    public function where($param, $regex, $nullable = false): self
    {
        $this->params[$param] = str_replace('(', '(?:', $regex);
        return $this;
    }

    /**
     * @param $param
     * @param $regex
     * @param bool $nullable
     * @return Route
     * @deprecated use Where method
     */
    public function width($param, $regex, $nullable = false): self
    {
        return $this->where($param, $regex, $nullable);
    }

}