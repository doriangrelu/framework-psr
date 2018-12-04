<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 04/12/2018
 * Time: 16:46
 */

namespace App\Provider;

use function DI\get;
use App\Framework\Auth\Auth;
use App\Framework\Auth\AuthInterface;
use Framework\Auth\User;
use Framework\Auth\UserInterface;
use Framework\Cookie\CookieInterface;
use Framework\Cookie\PHPCookie;
use Framework\Decorator\ValidatorDecorator;
use Framework\Provider\AbstractProvider;
use Framework\Router\RouterInterface;
use Framework\Session\PHPSession;
use Framework\Session\SessionInterface;
use Framework\Validator\ValidatorInterface;


class ProviderManager extends AbstractProvider
{

    /**
     * @throws \App\Framework\Exception\ProviderException
     */
    protected function _setProviders(): void
    {
        $this->_addDefinition(CookieInterface::class, get(PHPCookie::class));
        $this->_addDefinition(SessionInterface::class, get(PHPSession::class));
        $this->_addDefinition(RouterInterface::class, get(Router::class));
        $this->_addDefinition(ValidatorInterface::class, ValidatorDecorator::class);
    }

    /**
     * @throws \App\Framework\Exception\ProviderException
     */
    protected function _setAuthProviders(): void
    {
        $this->_addDefinition(AuthInterface::class, get(Auth::class));
        $this->_addDefinition(UserInterface::class, get(User::class));
    }


    protected function _setMiddlewaresProviders(): void
    {
        // TODO: Implement _setMiddlewaresProviders() method.
    }

    protected function _setTwigProviders(): void
    {
        // TODO: Implement _setTwigProviders() method.
    }

    protected function _setEventProviders(): void
    {
        // TODO: Implement _setEventProviders() method.
    }
}