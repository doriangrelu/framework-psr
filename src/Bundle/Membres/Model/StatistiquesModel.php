<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 22/12/2017
 * Time: 22:33
 */

namespace App\Bundle\Membres\Model;


use Framework\Model;
use Psr\Container\ContainerInterface;

class StatistiquesModel extends Model
{

    /**
     * @var Informations
     */
    protected $informationsModel;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->informationsModel = $this->container->get(Informations::class);
    }

    /**
     * @param int $idUsers
     * @return array
     */
    public function getStatistiques(int $idUsers): array
    {
        $enregistrements = $this->informationsModel->getTableauCompletNombreUtilisateursDerniersMois($idUsers);
        return [
            "moyenne" => (float)$this->moyenne($enregistrements),
            "mediane" => (float)$this->mediane($enregistrements),
            "ecartType"=> (float)$this->ecartType($enregistrements),
            "variance"=>pow((float)$this->ecartType($enregistrements), 2)
        ];

    }

    /**
     * @param array $donnees
     * @return float
     */
    function ecartType (array $donnees):float {
        $donnees=$this->makeClearTable($donnees);
        $population = count($donnees);
        if ($population != 0) {
            $somme_tableau = array_sum($donnees);
            $moyenne = $somme_tableau / $population;
            $ecart = [];
            for ($i = 0; $i < $population; $i++){
                $ecart_donnee = $donnees[$i] - $moyenne;
                $ecart_donnee_carre = bcpow($ecart_donnee, 2, 2);
                array_push($ecart, $ecart_donnee_carre);
            }
            $somme_ecart = array_sum($ecart);
            $division = $somme_ecart / $population;
            $ecart_type = bcsqrt ($division, 2);
        } else {
            return 0;
        }
        return $ecart_type;
    }


    /**
     * @param array $tableau
     * @return int
     */
    public function mediane(array $tableau): int
    {
        $tableau=$this->makeClearTable($tableau);
        $count = count($tableau);
        $middleval = floor(($count - 1) / 2);
        if ($count % 2) {
            $median = $tableau[$middleval];
        } else {
            $low = $tableau[$middleval];
            $high = $tableau[$middleval + 1];
            $median = (($low + $high) / 2);
        }
        return $median;
    }

    private function makeClearTable(array $array):array
    {
        $retour=[];
        foreach ($array as $value){
            $retour[]=$value["Nombre d'entree"];
        }
        return $retour;
    }

    /**
     * @param array $tableau
     * @return float
     */
    private function moyenne(array $tableau): float
    {
        $cummul = 0;
        $tableau=$this->makeClearTable($tableau);
        foreach ($tableau as $valeur) {
            $cummul += $valeur;
        }
        return($cummul/count($tableau));
    }
}