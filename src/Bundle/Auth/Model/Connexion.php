<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 06/12/2017
 * Time: 19:52
 */

namespace App\Bundle\Auth\Model;


use App\Bundle\Auth\Utility\TempConnexion;
use App\Bundle\Database\Entity\Users;
use App\Bundle\Database\Table\UsersTable;
use Framework\Auth\User;
use Framework\Cookie\CookieInterface;
use Framework\Exception\RequiredTotpException;
use Framework\Model;
use Framework\Session\SessionInterface;
use OTPHP\TOTP;
use Psr\Container\ContainerInterface;

class Connexion extends Model
{
    const ID_SESSION = "auth.id";
    const TOKEN_SESSION = "auth.token";
    const ROLE_SESSION = "auth.role";
    const TEMP = "auth.temp";

    /**
     * @var UsersTable
     */
    private $usersTable;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var CookieInterface
     */
    private $cookie;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->usersTable = $this->table->load(UsersTable::class);
        $this->session = $container->get(SessionInterface::class);
        $this->cookie = $container->get(CookieInterface::class);
    }

    public function desactiverTotp()
    {
        $user = $this->userIsConnected();
        return $this->usersTable->update($user->getUserId(), ["totp_key" => NULL]);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function checkPasswordWhenUserConnected(string $password): bool
    {
        try {
            $user = $this->userIsConnected();
            $userEntity = $this->usersTable->find($user->getUserId());
            return $this->checkLoginPassword($userEntity->mail, $password);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function otp($secret = null):?array
    {
        $otp = TOTP::create($secret);
        $user = $this->userIsConnected();
        $otp->setLabel($user->getUsername());
        $otp->setIssuer("Gestion entreprise");
        if (!is_null($user)) {
            return [
                "code" => $otp->getQrCodeUri(),
                "secret" => $otp->getSecret()
            ];
        }
        return null;

    }

    public function activerToTp(string $secret): bool
    {
        $user = $this->userIsConnected();
        return $this->usersTable->update($user->getUserId(), ["totp_key" => $secret]);
    }

    /**
     * @param int|null $idUsers
     * @return bool
     */
    public function activedTOTP(?int $idUsers = null): bool
    {
        $this->getIdUsers($idUsers);
        $users = $this->usersTable->find($idUsers);
        if (!is_null($users) && !empty($users->totpKey)) {
            return true;
        }
        return false;
    }

    private function getIdUsers(&$container): void
    {
        if (is_null($container)) {
            $container = $this->userIsConnected()->getUserId();
        }
    }


    /**
     * @param TempConnexion $connexion
     * @return bool
     */
    public function checkTempConnexion(TempConnexion $connexion): bool
    {
        try {
            $users = $this->usersTable->findBy([
                "mail" => $connexion->email,
                "token" => $connexion->token
            ]);
            if (!is_null($users)) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param string $mail
     * @param string $password
     * @return bool
     */
    public function checkLoginPassword(string $mail, string $password): bool
    {
        try {
            $users = $this->usersTable->findBy(["mail" => $mail]);
        } catch (\Exception $e) {
            return false;
        }
        return $this->password->verify($password, $users->password);

    }

    /**
     * @param string $mail
     * @param string|null $password
     * @param bool $checkTotp
     * @param bool $persist
     * @return bool
     * @throws RequiredTotpException
     */
    public function setConnexion(string $mail, string $password = null, bool $checkTotp = false, bool $persist = false): bool
    {
        $users = $this->usersTable->findBy(["mail" => $mail]);
        if ($checkTotp == true) {
            $this->setSessionConnexion($users);
            return true;
        } else {
            if ($this->checkLoginPassword($mail, $password)) {
                if ($this->activedTOTP($users->id)) {
                    $token = $this->utility->generateToken();
                    $tempSession = new TempConnexion();
                    $tempSession->email = $mail;
                    $tempSession->token = $token;
                    $this->session->set(self::TEMP, $tempSession);
                    $this->usersTable->update($users->id, ["token" => $token]);
                    throw new RequiredTotpException();
                }
                $token=$this->setSessionConnexion($users);
                if($persist){
                    $this->persist($users, $token);
                }
                return true;
            }
            return false;
        }


    }

    /**
     * @param Users $users
     * @param string $token
     */
    private function persist(Users $users, string $token): void
    {
        $this->cookie->set(self::ID_SESSION, $users->id);
        $this->cookie->set(self::ROLE_SESSION, (is_array($users->idRoles) ? $users->idRoles : [$users->idRoles]));
        $this->cookie->set(self::TOKEN_SESSION, $token);
    }

    /**
     * @param Users $users
     * @return string
     */
    private function setSessionConnexion(Users $users):string
    {
        $token = $this->utility->generateToken();
        $this->session->set(self::ID_SESSION, $users->id);
        $this->session->set(self::ROLE_SESSION, (is_array($users->idRoles) ? $users->idRoles : [$users->idRoles]));
        $this->session->set(self::TOKEN_SESSION, $token);
        $this->usersTable->update($users->id, ["token" => $token]);
        return $token;
    }

    /**
     * @param $token
     * @param $id
     * @param $roles
     */
    private function getInformationsConnexionCookieSession(&$token, &$id, &$roles):void
    {
        $token = $this->session->get(self::TOKEN_SESSION);
        $id = $this->session->get(self::ID_SESSION);
        $roles = $this->session->get(self::ROLE_SESSION);
        if ($token==null || $id==null || $roles==null) {
            $token = $this->cookie->get(self::TOKEN_SESSION);
            $id = $this->cookie->get(self::ID_SESSION);
            $roles = $this->cookie->get(self::ROLE_SESSION);
            if($token!=null){
                dd($token);
            }
        }
    }

    /**
     * Renvoi un User si connecté sinon null
     * @return User|null
     */
    public function userIsConnected():?User
    {
        /*$token = $this->session->get(self::TOKEN_SESSION);
        $id = $this->session->get(self::ID_SESSION);
        $roles = $this->session->get(self::ROLE_SESSION);*/
        $token=null;
        $id=null;
        $roles=null;
        $this->getInformationsConnexionCookieSession($token, $id, $roles);

        if ($token && $id && $roles) {

            try {
                $users = $this->usersTable->find($id);

                if ($users->token == $token && in_array($users->idRoles, $roles)) {
                    return new User($users->mail, $id, $roles);
                }
            } catch (\Exception $exception) {

                return null;
            }
        }

        return null;
    }

}