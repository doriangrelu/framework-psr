<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 13/02/2018
 * Time: 14:33
 */

namespace App\Bundle\Parametres\Controller;


use App\Bundle\Auth\Model\Connexion;
use App\Bundle\Database\Table\UsersTable;
use App\Bundle\Parametres\ParametresBundle;
use Framework\Middleware\CsrfMiddleware;
use OTPHP\TOTP;

class Pages extends ParametresBundle
{
    /**
     * @var Connexion
     */
    private $connexionModel;

    public function initialize(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Container\ContainerInterface $container)
    {
        parent::initialize($request, $container); // TODO: Change the autogenerated stub
        $this->connexionModel = $this->container->get(Connexion::class);
    }

    public function index()
    {
        $usersTable = $this->container->get(UsersTable::class);
        $connexion = $this->container->get(Connexion::class);
        $totpKey = $usersTable->find($this->usersId)->totpKey;
        $code = null;
        if (!is_null($totpKey)) {
            $code = $otp = $connexion->otp($totpKey)["code"];
        }

        $file = ROOT . "config" . DS . "log" . DS . $this->usersId . ".txt";
        $log = "Nothing";
        if (is_file($file)) {
            $log = file_get_contents($file);
        }
        $this->renderer->setActive("params");
        $this->renderer->make([
            "code"=>$code,
            "log" => $log,
            "totp" => $this->connexionModel->activedTOTP($this->usersId)
        ]);
        return $this->renderer->render("Home");
    }

    public function getTotpQrCode()
    {
        $csrf = $this->container->get(CsrfMiddleware::class);
        $usersTable = $this->container->get(UsersTable::class);
        $connexion = $this->container->get(Connexion::class);
        if (isset($this->parsedBody["password"]) && $connexion->checkPasswordWhenUserConnected($this->parsedBody["password"])) {
            $totpKey = $usersTable->find($this->usersId)->totpKey;
            $code = null;
            if (!is_null($totpKey)) {
                $code = $otp = $connexion->otp($totpKey)["code"];
            }
            return json_encode([
                "code" => $code,
                "_csrf" => $csrf->generateToken()
            ]);
        }
        return json_encode([
            "code" => null,
            "_csrf" => $csrf->generateToken()
        ]);
    }

}