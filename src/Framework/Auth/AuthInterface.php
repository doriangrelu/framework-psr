<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 26/11/2017
 * Time: 16:37
 */

namespace Framework\Auth;


interface AuthInterface
{
    /**
     * Retourne une intance de l'objet user, correspondant à l'utilisateur connecté. Si aucun utilisateur connecté null.
     * @return null|UserInterface
     */
    public function getUser(): ?UserInterface;
}