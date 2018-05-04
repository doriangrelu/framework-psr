<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 14/12/2017
 * Time: 11:54
 */

namespace Framework\Twig;


class JsExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction("js", [$this, "js"], [
                'is_safe' => ['html']
            ]),
            new \Twig_SimpleFunction("cdn_js", [$this, "cdnJs"], [
                'is_safe' => ['html']
            ])
        ];
    }

    public function cdnJs(string $uri)
    {
        return "<script src='$uri'></script>";
    }

    public function js(string $fileName, bool $inCurrentFolder = true)
    {
        $jsPath = WEB_ROOT . "js/";
        if (!$inCurrentFolder) {
            $jsPath = WEB_ROOT;
        }
        return "<script src='{$jsPath}{$fileName}'></script>";
    }
}