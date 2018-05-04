<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 26/11/2017
 * Time: 16:56
 */

namespace Framework\Auth;


use App\Bundle\Auth\Model\Connexion;
use Psr\Container\ContainerInterface;

class DatabaseAuth implements AuthInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Connexion
     */
    private $connexionModel;

    /**
     * DatabaseAuth constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->connexionModel = $container->get(Connexion::class);

    }

    /**
     * Retourne une intance de l'objet user, correspondant à l'utilisateur connecté. Si aucun utilisateur connecté null.
     * @return null|UserInterface
     */
    public function getUser(): ?UserInterface
    {
        return $this->connexionModel->userIsConnected();
    }
}