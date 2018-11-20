<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 20/11/2018
 * Time: 14:54
 */

namespace Framework\Utility;


trait RequestUtility
{

    protected function isAjaxRequest():bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

}