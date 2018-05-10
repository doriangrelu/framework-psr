<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 08/05/2018
 * Time: 10:14
 */

namespace App\Framework\Exception;


use Throwable;

class UnsupportedOperationException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}