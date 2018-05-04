<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 01/12/17
 * Time: 12:38
 */

namespace Framework\Renderer;


use Framework\Utility\ClassNameUtility;
use PHPUnit\Runner\Exception;

class RendererFactory
{
    use ClassNameUtility;
    /**
     * @var
     */
    private $className;

    /**
     * @var string
     */
    private $layout = "Default";
    /**
     * @var RendererEnvironment
     */
    private $rendererEnvironment;

    /**
     * RendererFactory constructor.
     * @param string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
        $this->rendererEnvironment = new RendererEnvironment();
    }

    public function getRendererEnvironement():RendererEnvironment
    {
        $this->setViewPath();
        $this->setLayoutDirname();
        $this->setElementsDirName();
        $this->setLayoutFileName();
        return $this->rendererEnvironment;
    }

    public function setLayout(string $name)
    {
        $this->layout = $name;
    }

    private function setLayoutFileName()
    {
        $this->rendererEnvironment->setLayoutFileName(ucfirst($this->layout).'.twig');
    }

    private function setLayoutDirname()
    {
        $elementLayoutDirname = TEMPLATE . "Layout";
        if (is_dir($elementLayoutDirname)) {
            $this->rendererEnvironment->setLayoutDirname($elementLayoutDirname);
        } else {
            throw new \Exception("Il manque le dossier <$elementLayoutDirname>");
        }
    }

    private function setViewPath()
    {
        $viewPath = BUNDLE . $this->getBundleName($this->className) . DS . "Views" . DS . $this->getControllerName($this->className);
        if (is_dir($viewPath)) {
            $this->rendererEnvironment->setViewPath($viewPath);
        } else {
            throw new Exception("Il manque le chemin d'accès aux vue suivant: <$viewPath>");
        }
    }

    private function setElementsDirName()
    {
        $elementDirname = TEMPLATE . "Elements";
        if (is_dir($elementDirname)) {
            $this->rendererEnvironment->setElementsDirname($elementDirname);
        } else {
            throw new \Exception("Il manque le dossier <$elementDirname>");
        }
    }

    private function setElementsLayout()
    {
        $elementLayoutDirname = TEMPLATE . "Elements" . DS . "Layout" . DS . ucfirst($this->layout);
        if (is_dir($elementLayoutDirname)) {
            $this->rendererEnvironment->setLayoutDirname($elementLayoutDirname);
        } else {
            throw new \Exception("Il manque le dossier <$elementLayoutDirname>");
        }
    }


}