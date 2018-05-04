<?php
namespace Framework\Twig;

use Framework\Session\FlashService;
use Framework\Session\PHPSession;
use Framework\Session\SessionInterface;
use Psr\Container\ContainerInterface;

class FlashExtension extends \Twig_Extension
{

    /**
     * @var FlashService
     */
    private $flashService;

    public function __construct(ContainerInterface $container)
    {
        $this->flashService = $container->get(FlashService::class);
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('flash', [$this, 'getFlash'], [
                'is_safe' => ['html']
            ])
        ];
    }

    public function getFlash($type): ?string
    {
        return $this->flashService->get($type);
    }
}
