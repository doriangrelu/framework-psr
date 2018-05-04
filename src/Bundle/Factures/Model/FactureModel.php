<?php

namespace App\Bundle\Factures\Model;

use App\Bundle\Factures\Exceptions\FactureException;
use App\Bundle\Membres\Model\QuotationsModel;
use Framework\Model;

/**
 * Created by PhpStorm.
 * User: doria
 * Date: 02/04/2018
 * Time: 12:06
 */
class FactureModel extends Model
{


    /**
     * FactureModel constructor.
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(\Psr\Container\ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @param int $idUsers
     * @param string $slug
     * @return bool
     */
    public function devisExiste(int $idUsers, string $slug): bool
    {
        $devis = $this->container->get(QuotationsModel::class);
        return $devis->devisExist($idUsers, $slug);
    }

    /**
     * @param int $idUser
     * @param string $slug
     * @return bool
     * @throws FactureException
     */
    public function factureExiste(int $idUser, string $slug): bool
    {
        $retour = false;

        return $retour;
    }

}