<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 22/11/17
 * Time: 14:29
 */

namespace Framework;


use Framework\Renderer\RendererFactory;
use Psr\Container\ContainerInterface;

class Renderer
{
    /**
     * @var array
     */
    private $args = [];

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RendererFactory
     */
    private $factory;

    /**
     * @var array
     */
    private $activeTable;

    /**
     * Renderer constructor.
     * @param string $controller
     * @param ContainerInterface $container
     * @param null|string $layout
     */
    public function __construct(string $controller, ContainerInterface $container, ?string $layout = null)
    {

        $this->factory = new RendererFactory($controller);
        $this->container = $container;
        $this->activeTable = [];

    }


    /**
     * @return \Twig_Loader_Filesystem
     */
    private function getLoader(): \Twig_Loader_Filesystem
    {
        $rendererEnvironement = $this->factory->getRendererEnvironement();
        return new \Twig_Loader_Filesystem([
            $rendererEnvironement->getElementsDirname(),
            $rendererEnvironement->getViewPath(),
            $rendererEnvironement->getLayoutDirname()
        ]);
    }

    public function setActive(string $name, int $rank = 1)
    {
        $this->activeTable[$rank] = $name;
    }

    /**
     * @return \Twig_Environment
     */
    private function getTwig(): \Twig_Environment
    {
        $twig = new \Twig_Environment($this->getLoader(), [
            "cache" => false
        ]);
        if ($this->container->has('twig.extensions')) {
            foreach ($this->container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }
        $twig->addExtension(new \Twig_Extensions_Extension_Text());
        return $twig;
    }


    /**
     * @param string $name
     * @throws \Exception
     */
    public function setLayout(string $name)
    {
        $this->factory->setLayout($name);
    }


    /**
     * @param $args
     * @param string|null $value
     */
    public function make($args, string $value = null): void
    {
        if (!is_array($args)) {
            $args = [$args => $value];
        }
        $this->args = array_merge($this->args, $args);
    }

    /**
     * @param string $viewName
     * @return string
     * @throws \Exception
     */
    public function render(string $viewName): string
    {
        $rendererEnvironement = $this->factory->getRendererEnvironement();

        $this->make([
            "parent_template" => $this->factory->getRendererEnvironement()->getLayoutFileName(),
            "active" => $this->activeTable
        ]);
        $viewName = ucfirst($viewName);
        $viewFile = $rendererEnvironement->getViewPath() . DS . $viewName . '.twig';
        if (!is_file($viewFile)) {
            throw new \Exception("La vue <$viewName> n'existe pas dans le dossier <{$rendererEnvironement->getViewPath()}>");
        }
        $template = $this->getTwig()->load("$viewName.twig");
        return $template->render($this->args);
    }

}