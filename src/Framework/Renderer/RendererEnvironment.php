<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 01/12/17
 * Time: 12:36
 */

namespace Framework\Renderer;


class RendererEnvironment
{
    /**
     * @var string
     */
    private $viewPath;
    /**
     * @var
     */
    private $layoutFileName;
    /**
     * @var
     */
    private $layoutDirname;
    /**
     * @var
     */
    private $elementsDirname;

    /**
     * @return mixed
     */
    public function getLayoutFileName()
    {
        return $this->layoutFileName;
    }

    /**
     * @param mixed $layoutFileName
     */
    public function setLayoutFileName($layoutFileName)
    {
        $this->layoutFileName = $layoutFileName;
    }

    /**
     * @return mixed
     */
    public function getLayoutDirname()
    {
        return $this->layoutDirname;
    }

    /**
     * @param mixed $layoutDirname
     */
    public function setLayoutDirname($layoutDirname)
    {
        $this->layoutDirname = $layoutDirname;
    }

    /**
     * @return mixed
     */
    public function getElementsDirname()
    {
        return $this->elementsDirname;
    }

    /**
     * @param mixed $elementsDirname
     */
    public function setElementsDirname($elementsDirname)
    {
        $this->elementsDirname = $elementsDirname;
    }



    /**
     * @param string $viewPath
     */
    public function setViewPath(string $viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
    }




}