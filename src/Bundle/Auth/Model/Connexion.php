<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 06/12/2017
 * Time: 19:52
 */

namespace App\Bundle\Auth\Model;

use App\Models\Users;
use Framework\Cookie\CookieInterface;
use Framework\Database\NoRecordException;
use Framework\Model;
use Framework\Session\SessionInterface;
use Psr\Container\ContainerInterface;

class Connexion
{

    private const ID = "id";
    private const TOKEN = "token";

    /**
     * @var Users
     */
    private $users;

    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var CookieInterface
     */
    private $cookie;

    /**
     * @var bool
     */
    private $withTokenSecurity;

    public function __construct(ContainerInterface $container)
    {
       // parent::__construct($container->get(\PDO::class));
        $this->users = $container->get(Users::class);
        $this->session = $container->get(SessionInterface::class);
        $this->cookie = $container->get(CookieInterface::class);
        $this->table = $container->get("auth")["userTable"];
        $this->withTokenSecurity = $container->get("auth")["tokenSecurity"];
    }

    public function getUser(): ?Users
    {
        /** DEV */
        $this->cookie->set(self::ID, 1);
        $this->cookie->set(self::TOKEN, "aa");

        $params = $this->readSessionOrCookie();
        if(is_null($params)){
            return null;
        }
        $user = $this->users->select()->where("`".self::ID."`=:".self::ID, "`".self::TOKEN."`=:".self::TOKEN)->params($params);
        try{
            $user = $user->fetchOrFail();
            return $user;
        } catch(NoRecordException $e){
            return null;
        }
    }

    private function readSessionOrCookie(): ?array
    {
        if ($this->session->exist(self::ID) && $this->session->exist(self::TOKEN)) {
            return [
                self::ID => $this->session->get(self::ID),
                self::TOKEN => $this->session->get(self::TOKEN)
            ];
        } else {
            if($this->cookie->exist(self::ID) && $this->cookie->exist(self::TOKEN)){
                return [
                    self::ID => $this->cookie->get(self::ID),
                    self::TOKEN => $this->cookie->get(self::TOKEN)
                ];
            }
        }
        return null;
    }


}