<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 11/11/2018
 * Time: 15:50
 */

namespace App\Framework\Auth;


use Framework\Auth\UserInterface;
use Framework\Session\SessionInterface;
use Psr\Container\ContainerInterface;

class Auth implements AuthInterface
{

    const S_AUTH_KEY = 'Auth';
    const S_UID = 'user.id';
    const S_UNAME = 'user.name';
    const S_UROLES = 'user.roles';

    private $_allowedMethod = [];
    private $_allowedController = false;
    private $_session;
    private $_user;


    /**
     * Auth constructor.
     * @param SessionInterface $sessions
     * @param UserInterface $user
     */
    public function __construct(SessionInterface $sessions, UserInterface $user)
    {
        $this->_session = $sessions;
        $this->_user = $user;
    }

    /**
     * @return Auth
     */
    public function allowController(): AuthInterface
    {
        $this->_allowedController = true;
        return $this;
    }

    /**
     * @param $allowedMethod
     * @return Auth
     */
    public function allowMethods($allowedMethod): AuthInterface
    {
        if (!is_array($allowedMethod)) {
            $allowedMethod = [$allowedMethod];
        }
        $this->_allowedMethod = array_merge($this->_allowedMethod, $allowedMethod);
        return $this;
    }

    /**
     * @param string $requestedMethod
     * @return bool
     */
    public function access(string $requestedMethod): bool
    {
        if ($this->_allowedController) {
            return true;
        }
        return in_array($requestedMethod, $this->_allowedMethod);
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        if ($this->_session->exist(self::S_AUTH_KEY)) {
            $this->_user->unserialized($this->_session->get(self::S_AUTH_KEY));
            return $this->_user;
        }
        return null;
    }
}