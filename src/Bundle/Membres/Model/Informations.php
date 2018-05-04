<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 12/12/2017
 * Time: 12:16
 */

namespace App\Bundle\Membres\Model;


use App\Bundle\Database\Table\UsersTable;
use Framework\Model;
use Psr\Container\ContainerInterface;

class Informations extends Model
{
    /**
     * @var UsersTable
     */
    protected $usersTable;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->usersTable = $this->table->load(UsersTable::class);
    }

    public function setInformationsEntreprise(int $id, array $params): bool
    {
        return $this->usersTable->update($id, $params);
    }

    public function getTableauCompletNombreUtilisateursDerniersMois(int $idUsers): array
    {
        //vnd.ms-excel
        $perMois = [];
        $listeMois = getLatestMonth();
        foreach ($listeMois as $mois) {
            $perMois[] = [
                "Mois" => "{$mois["moisComplet"]} {$mois["annee"]}",
                "Nombre d'entree" => $this->getNombreNouveauClientPourUnmois($mois["mois"], $idUsers)
            ];

        }
        ksort($perMois);
        return $perMois;
    }

    public function getNombreNouveauClientParMois(int $idUsers): array
    {
        $perMois = [
            ["Mois, année", "Nombre de nouveaux clients"]
        ];
        $listeMois = getLatestMonth();
        foreach ($listeMois as $mois) {
            $perMois[] = [
                $mois["moisComplet"] . " " . $mois["annee"],
                $this->getNombreNouveauClientPourUnmois($mois["mois"], $idUsers)
            ];
        }
        return $perMois;
    }

    public function getNomDerniersMois(int $nb = 6): array
    {
        $perMois = [];
        $listeMois = getLatestMonth();
        foreach ($listeMois as $mois) {

        }
        return $perMois;
    }

    public function getNombreNouveauClientPourUnmois(int $moi, int $idUsers):int
    {
        $pro = $this->getNbNouveauClientPerMoisPro($moi, $idUsers);
        $part = $this->getNbNouveauClientPerMoisPart($moi, $idUsers);
        return $pro+$part;
    }

    public function getNbNouveauClientPerMoisPro(int $moi = null, int $idUsers)
    {
        $joins = ["id" => $idUsers];
        $cond="";
        if (!is_null($moi)) {
            $cond = "AND month(created_at)=:mois";
            $joins["mois"] = $moi;
        }
        $statement = "select id_users, count(*) as nombre from professionnal where id_users=:id $cond GROUP BY id_users;";
        $request=$this->query()->query($statement, $joins)["content"][0];
        if(!isset($request->nombre)){
            return 0;
        }
        return $request->nombre;
    }

    /**
     * @param int $idUsers
     * @return float
     */
    public function getPercentPro(int $idUsers):float
    {
        $pro=$this->getNbNouveauClientPerMoisPro(null, $idUsers);
        $part=$this->getNbNouveauClientPerMoisPart(null, $idUsers);
        $total=$pro+$part;
        return floor(($pro*100)/$total);
    }

    /**
     * @param int $idUsers
     * @return float
     */
    public function getPercentPart(int $idUsers):float
    {
        $pro=$this->getNbNouveauClientPerMoisPro(null, $idUsers);
        $part=$this->getNbNouveauClientPerMoisPart(null, $idUsers);
        $total=$pro+$part;
        return floor(($part*100)/$total);
    }

    public function getNbNouveauClientPerMoisPart(int $moi = null, int $idUsers)
    {
        $joins = ["id" => $idUsers];
        $cond="";
        if (!is_null($moi)) {
            $cond = "AND month(created_at)=:mois";
            $joins["mois"] = $moi;
        }
        $statment= "select id_users, count(*) as nombre from individual where id_users=:id $cond GROUP BY id_users;";
        $request=$this->query()->query($statment, $joins)["content"][0];
        if(!isset($request->nombre)){
            return 0;
        }
        return $request->nombre;
    }


    public function setInformations(int $id, array $params): bool
    {
        return $this->usersTable->update($id, $params);
    }
}