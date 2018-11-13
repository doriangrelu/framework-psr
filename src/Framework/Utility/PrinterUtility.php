<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 13/11/2018
 * Time: 17:45
 */

namespace Framework\Utility;


use DOMDocument;

trait PrinterUtility
{
    /**
     * @param $buffer
     * @return string
     */
    protected function _prettyHTML($buffer): string
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadHTML($buffer);
        $dom->formatOutput = true;
        return ($dom->saveHTML());
    }
}