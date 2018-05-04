<?php

namespace Framework\Twig;

/**
 * Série d'extensions concernant les textes
 *
 * @package Framework\Twig
 */
class TextExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('d', [$this, "d"]),
            new \Twig_SimpleFunction('dd', [$this, "dd"]),
            new \Twig_SimpleFunction('instanceof', [$this, "instanceOf"]),
            new \Twig_SimpleFunction('text', [$this, 'nullText']),
            new \Twig_SimpleFunction('adress', [$this, "adress"])
        ];
    }

    public function adress(?string $adress, ?string $cp, ?string $city):string
    {
        $fullAdress="";
        $fullAdress.=empty($adress)?"":$adress;
        $fullAdress.=empty($cp)?"": " $cp";
        $fullAdress.=empty($city)?"":!empty($fullAdress)?", $city":$city;
        return $this->nullText($fullAdress);
    }

    public function nullText(?string $text, string $affichache="-"):string
    {
        if(empty($text)){
            return $affichache;
        }
        return $text;
    }

    public function instanceOf($var, $instance):bool
    {
        return $var instanceof $instance;
    }

    /**
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('excerpt', [$this, 'excerpt'])
        ];
    }

    public function d($debug)
    {
        dump($debug);
    }

    public function dd($debug)
    {
        dump($debug);
        die();
    }

    /**
     * Renvoie un extrait du contenu
     * @param string $content
     * @param int $maxLength
     * @return string
     */
    public function excerpt(?string $content, int $maxLength = 100): string
    {
        if (is_null($content)) {
            return '';
        }
        if (mb_strlen($content) > $maxLength) {
            $excerpt = mb_substr($content, 0, $maxLength);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }
}
