<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 22/11/17
 * Time: 14:29
 */

namespace Framework;


use App\Framework\Exception\RendererException;
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
    private $activeTable = [];

    /**
     * @var string
     */
    private $layout ="default";

    /**
     * Renderer constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * @return \Twig_Loader_Filesystem
     */
    private function getLoader(): \Twig_Loader_Filesystem
    {
        return new \Twig_Loader_Filesystem([
            TEMPLATE,
            TEMPLATE . "Layout"
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
            "cache" => Mode::is_prod()
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
        $this->layout = $name;
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
     * @throws RendererException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(string $viewName): string
    {
        $this->make([
            "parent_template" => $this->layout,
            "active" => $this->activeTable
        ]);
        $viewName = str_replace(".", DS, $viewName);
        $viewFile = TEMPLATE . $viewName . '.twig';
        if (!is_file($viewFile)) {
            throw new RendererException("La vue <$viewName> n'existe pas");
        }
        $template = $this->getTwig()->load("$viewName.twig");
        return $template->render($this->args);
    }

}