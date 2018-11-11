<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 11/11/2018
 * Time: 16:36
 */

namespace App\Framework\Exception\Http;


class ForbiddenHttpException extends HttpException
{
    public function __construct($statusPhrase = null, array $headers = array())
    {
        parent::__construct(403, $statusPhrase, $headers);
    }
}