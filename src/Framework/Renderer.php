<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 22/11/17
 * Time: 14:29
 */

namespace Framework;


use App\Framework\Exception\RendererException;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\StandardDebugBar;
use Framework\Renderer\RendererFactory;
use Framework\Utility\PrinterUtility;
use Psr\Container\ContainerInterface;

class Renderer
{

    use PrinterUtility;

    /**
     * @var array
     */
    private $args = [];

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $activeTable = [];

    /**
     * @var string
     */
    private $layout = "default";

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
            "cache" => Mode::DEVELOPPEMENT === true,
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
        $debugBar = $this->container->get(StandardDebugBar::class);
        $this->make([
            "parent_template" => $this->_getFullLayoutName(),
            "active" => $this->activeTable,
            "debugBar" => $this->container->get('mode') == Mode::DEVELOPPEMENT ? $debugBar : null,
        ]);
        $viewName = str_replace(".", DS, $viewName);
        $viewFile = TEMPLATE . $viewName . '.twig';
        if (!is_file($viewFile)) {
            throw new RendererException("Missing view $viewName");
        }
        $template = $this->getTwig()->load("$viewName.twig");
        $html = $template->render($this->args);

        if (class_exists(\tidy::class)) {
            $tidy = new \tidy();
            $config = array(
                'indent' => true,
                'output-xhtml' => true,
                'wrap' => 200);
            $tidy->parseString($html, $config, 'utf8');
            $tidy->cleanRepair();
            return $tidy;
        }

        return $html;


    }

    private function _getFullLayoutName(): string
    {
        return ucfirst($this->layout) . '.twig';
    }

}