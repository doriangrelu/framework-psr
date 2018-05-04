<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 14/12/2017
 * Time: 11:45
 */

namespace Framework\Twig;


class CssExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction("css", [$this, "css"], [
                'is_safe' => ['html']
            ]),
            new \Twig_SimpleFunction("cdn_css", [$this, "cdnCss"], [
                'is_safe' => ['html']
            ])
        ];
    }

    public function cdnCss(string $uri)
    {
        return "<link href='$uri' rel='stylesheet'>";
    }

    public function css(string $fileName, bool $inCurrentFolder = true)
    {
        $cssPath = WEB_ROOT . "css/";
        if (!$inCurrentFolder) {
            $cssPath = WEB_ROOT;
        }
        return "<link href='{$cssPath}{$fileName}' rel='stylesheet'>";
    }

}