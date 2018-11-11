<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 11/11/2018
 * Time: 16:37
 */

namespace App\Framework\Exception\Http;


class NotFoundHttpException extends HttpException
{
public function __construct($statusPhrase = null, array $headers = array())
{
    parent::__construct(404, $statusPhrase, $headers);
}
}