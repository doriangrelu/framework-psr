<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 15/12/2017
 * Time: 12:48
 */

namespace App\Bundle\Membres\Model;


use App\Bundle\Database\Table\IndividualTable;
use App\Bundle\Database\Table\ProfessionnalTable;
use Cake\Utility\Inflector;
use Framework\Database\QueryResult;
use Framework\Database\Table;
use Framework\Model;
use Framework\Validator;
use Psr\Container\ContainerInterface;

class Client extends Model
{

    /**
     * @var IndividualTable
     */
    private $individualTable;

    /**
     * @var ProfessionnalTable
     */
    private $professionnalTable;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->individualTable = $this->table->load(IndividualTable::class);
        $this->professionnalTable = $this->table->load(ProfessionnalTable::class);
    }

    /**
     * @param int $idClient
     * @param string $type
     * @param array|null $body
     * @return int|null
     */
    public function modifierType(int $idClient, string $type, ?array $body = []): ?int
    {
        $pdo = $this->container->get(\PDO::class);
        $pdo->beginTransaction();
        $newType = "professionnel";
        if ($type == "professionnel") {
            $newType = "particulier";
        }
        $oldTable = $this->getTableFromTypeClient($type);
        $entity = $oldTable->find($idClient);
        $oldTable->delete($idClient);
        $arrayFromEntity = $this->prepareEntityToInsert($entity);
        if ($newType == "professionnel") {
            $arrayFromEntity = array_merge($arrayFromEntity, $body);
        } else {
            $this->deleteKeys("siret", $arrayFromEntity);
            $this->deleteKeys("compagny_name", $arrayFromEntity);
        }
        $this->deleteKeys("id", $arrayFromEntity);
        $newTable = $this->getTableFromTypeClient($newType);
        if ($newTable->insert($arrayFromEntity)) {
            $lastId = $newTable->lastInsertId();
            $pdo->commit();
            return $lastId;
        }
        $pdo->rollBack();
        return null;
    }

    private function prepareEntityToInsert($entity): array
    {
        $table = (array)$entity;
        $newTable = [];
        foreach ($table as $key => $value) {
            $key = Inflector::underscore($key);
            if ($value instanceof \DateTime) {
                $value = $value->format("Y-m-d H:i:s");
            }
            $newTable[$key] = $value;
        }
        $this->setUpdatedAt($newTable);
        return $newTable;
    }


    /**
     * @param int $idUsers
     * @param $idClient
     * @param string $type
     * @return bool
     */
    public function clientAppartientA(int $idUsers, $idClient, string $type): bool
    {
        $by = [
            "id_users" => $idUsers,
            "id" => $idClient
        ];
        try {
            $this->getTableFromTypeClient($type)->findBy($by);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param int $idClient
     * @param array $params
     * @param string $type
     * @return bool
     */
    public function modifierClient(int $idClient, array $params, string $type): bool
    {
        $this->setUpdatedAt($params);
        return $this->getTableFromTypeClient($type)->update($idClient, $params);
    }

    /**
     * @param int $id
     * @param string $type
     * @return bool
     */
    public function supprimerClient(int $id, string $type): bool
    {
        return $this->getTableFromTypeClient($type)->delete($id);
    }

    /**
     * @param string $type
     * @return IndividualTable|ProfessionnalTable|Table
     */
    private function getTableFromTypeClient(string $type)
    {
        if ($type == "particulier") {
            return $this->individualTable;
        }
        return $this->professionnalTable;
    }

    /**
     * @param int $idUsers
     * @return QueryResult
     */
    public function getListeProfessionnel(int $idUsers): QueryResult
    {
        return $this->professionnalTable->findAll(["id_users" => $idUsers])->fetchAll();
    }

    /**
     * @param int $idUsers
     * @return QueryResult
     */
    public function getListeParticulier(int $idUsers): QueryResult
    {
        return $this->individualTable->findAll(["id_users"=>$idUsers])->fetchAll();
    }

    /**
     * Retourne la liste de client pro et particulier triée par ordre de modification plus récentes
     * @param int $idUsers
     * @return array
     */
    public function getListeClients(int $idUsers): array
    {
        $results = [];
        $pro = $this->professionnalTable->findAll(["id_users" => $idUsers])->fetchAll();
        $par = $this->individualTable->findAll(["id_users" => $idUsers])->fetchAll();
        foreach ($pro as $pr) {
            $results[] = $pr;
        }
        foreach ($par as $pr) {
            $results[] = $pr;
        }
        return $results;
    }

    public function viderBaseDeDonnees(int $userId): bool
    {
        $pdo = $this->container->get(\PDO::class);
        $pdo->beginTransaction();
        $pro = $this->professionnalTable->deleteBy(["id_users" => $userId]);
        $part = $this->individualTable->deleteBy(["id_users" => $userId]);
        if ($part && $pro) {
            $pdo->commit();
            return true;
        }
        $pdo->rollBack();
        return false;
    }

    /**
     * @param int $idUsers
     * @param array $csvContent
     * @return bool
     * @throws \Exception
     */
    public function importerCsv(int $idUsers, array $csvContent): bool
    {
        $csvContent = [
            ["first_name", "last_name", "clientType"],
            ["Dorian", "Grelu", "part"],
            ["Dorian", "Grelu", "part"],
            ["Dorian", "Grelu", "part"],
            ["Dorian", "Grelu", "part"],
            ["Dorian", "Grelu", "part"],
            ["Dorian", "Grelu", "part"],
            ["Dorian", "Grelu", "part"],
            ["Dorian", "Grelu", "part"],
            ["Dorian", "Grelu", "part"],
            ["Dorian", "Grelu", "part"],
            ["Dorian", "Grelu", "part"]
        ];
        $forInsert = [];
        if (count($csvContent) > 0) {
            $cursor = 0;
            $header = $csvContent[0];
            for ($i = 1; $i < count($csvContent); $i++) {
                $forInsert[$cursor] = [];
                if (count($header) == count($csvContent[$i])) {
                    for ($n = 0; $n < count($csvContent[$i]); $n++) {
                        $forInsert[$cursor][$header[$n]] = $csvContent[$i][$n];
                    }
                    $cursor++;
                } else {
                    throw new \Exception("Parse CSV Error");
                }
            }
            $pdo = $this->container->get(\PDO::class);
            $pdo->beginTransaction();
            foreach ($forInsert as $line) {
                try {
                    $validator = new Validator($line);
                    $validator->required("first_name", "last_name")
                        ->notEmpty("first_name", "last_name");
                    if ($line["clientType"] == "pro") {
                        $validator->required("siret", "compagny_name")
                            ->notEmpty("compagny_name")
                            ->siret("siret");
                    }
                    if (!$validator->isValid() || !$this->ajouter($idUsers, $line)) {
                        $pdo->rollBack();
                        return false;
                    }
                } catch (\Exception $e) {
                    $pdo->rollBack();
                    return false;
                }
            }
            $pdo->commit();
            return true;
        }
        return false;
    }

    /**
     * @param int $idUsers
     * @param int $idClient
     * @param string $typeClient
     * @return bool|mixed
     */
    public function getClient(int $idUsers, int $idClient, string $typeClient)
    {
        $by = [
            "id_users" => $idUsers,
            "id" => $idClient
        ];
        if ($typeClient == "particulier") {
            return $this->individualTable->findBy($by);
        }
        return $this->professionnalTable->findBy($by);
    }

    /**
     * Tri les clients pro et individuels par date qui dispose d'une meilleur compléxité comparé à l'ancien algorithme
     * @param $array
     * @return array
     */
    function quick_sort($array)
    {
        $length = count($array);
        if ($length <= 1) {
            return $array;
        } else {
            $pivot = $array[0];
            $left = $right = array();
            for ($i = 1; $i < count($array); $i++) {
                if ($array[$i]->updatedAt > $pivot->updatedAt) {
                    $left[] = $array[$i];
                } else {
                    $right[] = $array[$i];
                }
            }
            return array_merge($this->quick_sort($left), array($pivot), $this->quick_sort($right));
        }
    }

    /**
     * Améliorer la compléxité de l'algorithme en implémenter un quicksort plus tard
     * @param array $collection
     * @return array
     * @deprecated use quick_sort
     */
    private function sortIndividualProfessionnal(array $collection): array
    {
        $results = $collection;
        for ($i = 0; $i < count($results); $i++) {
            for ($n = 0; $n < count($results); $n++) {
                if ($results[$i]->updatedAt > $results[$n]->updatedAt) {
                    $temp = $results[$n];
                    $results[$n] = $results[$i];
                    $results[$i] = $temp;
                }
            }
        }
        return $results;
    }

    /**
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function ajouter(int $id, array $params): bool
    {
        $this->checkEmptyField($params);
        $this->setCreatedAt($params);
        $type = $params["clientType"];
        $this->deleteKeys("clientType", $params);
        $this->addKey("id_users", $id, $params);

        if ($type == "part") {
            return $this->individualTable->insert($params);
        } elseif ($type == "pro") {
            return $this->professionnalTable->insert($params);
        } else {
            return false;
        }
        return true;
    }

    /**
     * @param $params
     */
    private function checkEmptyField(&$params)
    {
        foreach ($params as $column => $param) {
            if (empty($param)) {
                $this->deleteKeys($column, $params);
            }
        }
    }

}