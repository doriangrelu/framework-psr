<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 14/12/2017
 * Time: 11:22
 */

namespace Framework\Twig;


class ActiveExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('active', [$this, 'isActive'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ])
        ];
    }
    public function isActive($context, string $name, int $rank=1)
    {
        if(isset($context["active"][$rank]) && $context["active"][$rank]==$name){
            return "active";
        }
        return null;
    }
}