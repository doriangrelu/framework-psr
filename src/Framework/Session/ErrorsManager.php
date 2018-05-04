<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 05/12/2017
 * Time: 12:02
 */

namespace Framework\Session;


use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class ErrorsManager
{
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session=$session;
    }

    public function setValidator(Validator $validator)
    {
        $this->session->set("validator", $validator);
    }

    public function getValidator():?Validator
    {
        return $this->get("validator");
    }

    public function setValues(array $body)
    {
        $this->session->set("values", $body);
    }

    public function getValues():?array
    {
        return $this->get("values");
    }

    private function get(string $key)
    {
        if($this->session->get($key)){
            $values=$this->session->get($key);
            $this->session->delete($key);
            return $values;
        }
        return null;
    }

}