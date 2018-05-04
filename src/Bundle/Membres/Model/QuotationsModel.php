<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 04/01/2018
 * Time: 18:19
 */

namespace App\Bundle\Membres\Model;


use App\Bundle\Database\Table\IndividualTable;
use App\Bundle\Database\Table\LinesTable;
use App\Bundle\Database\Table\PaymentsTable;
use App\Bundle\Database\Table\PaymentsTypeTable;
use App\Bundle\Database\Table\ProfessionnalTable;
use App\Bundle\Database\Table\QuotationStatusTable;
use App\Bundle\Database\Table\QuotationTable;
use App\Bundle\Database\Table\StatusTable;
use App\Bundle\Database\Table\UnityTable;
use Framework\Database\NoRecordException;
use Framework\Database\Query;
use Framework\Model;
use Psr\Container\ContainerInterface;

class QuotationsModel extends Model
{
    const EN_COURS_DE_SAISIE = "en-cours-de-saisie";
    const EN_POURPARLER = "en-pourparler";
    const SAISIE_TERMINEE = "saisie-cloture";

    /**
     * @var ProfessionnalTable
     */
    protected $professionnalTable;

    /**
     * @var IndividualTable
     */
    protected $individualTable;

    /**
     * @var PaymentsTable
     */
    private $paymentsTable;

    /**
     * @var UnityTable
     */
    private $unityTable;

    /**
     * @var QuotationTable
     */
    private $quotationTable;

    /**
     * @var LinesTable
     */
    private $lineTable;

    /**
     * @var PaymentsTypeTable
     */
    private $paymentsTypeTable;

    /**
     * @var QuotationStatusTable
     */
    private $quotationStatusTable;

    /**
     * @var StatusTable
     */
    private $statusTable;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->unityTable = $this->table->load(UnityTable::class);
        $this->quotationTable = $this->table->load(QuotationTable::class);
        $this->lineTable = $this->table->load(LinesTable::class);
        $this->paymentsTable = $this->table->load(PaymentsTable::class);
        $this->paymentsTypeTable = $this->table->load(PaymentsTypeTable::class);
        $this->individualTable = $this->table->load(IndividualTable::class);
        $this->professionnalTable = $this->table->load(ProfessionnalTable::class);
        $this->quotationStatusTable = $this->table->load(QuotationStatusTable::class);
        $this->statusTable = $this->table->load(StatusTable::class);
    }

    public function devisExist(int $idUsers, string $slug): bool
    {
        try {
            $this->quotationTable->findBy(["id_users" => $idUsers, "slug" => $slug]);
            return true;
        } catch (NoRecordException $e) {
            return false;
        }
    }

    /**
     * @param int $idUsers
     * @param int $idQuotation
     * @param array $params
     * @return bool
     * @internal param array $lines
     */
    public function modifier(int $idUsers, int $idQuotation, array $params): bool
    {
        $this->db->beginTransaction();
        try {
            $this->addKey("id_users", $idUsers, $params);
            $this->deleteKeys("id_devis", $params);
            $this->deleteKeys("id_users", $params);
            //$this->notRequired("join", $params);
            $this->notRequired("deadline", $params);
            $lines = $params["line"];
            $this->deleteKeys("line", $params);
            $this->setUpdatedAt($params);
            $acomptes = [];
            if (isset($params["acompte"])) {
                $acomptes = $params["acompte"];
                $this->deleteKeys("acompte", $params);
            }
            $quotationStatus = [];
            $query = $this->container->get(Query::class);
            $idStatus = $this->statusTable->find($params["id_status"])->id;
            if ($this->statusExist($idQuotation, $params["id_status"])) {
                $this->setUpdatedAt($quotationStatus);
                if (!$query->update("quotation_status", $quotationStatus, ["id_status" => $idStatus, "id_quotations" => $idQuotation])) {
                    throw new \Exception();
                }
            } else {
                $quotationStatus["id_quotations"] = $idQuotation;
                $quotationStatus["id_status"] = $idStatus;
                $this->setCreatedAt($quotationStatus);
                if (!$this->quotationStatusTable->insert($quotationStatus)) {
                    throw new \Exception();
                }
            }
            if (count($lines) > 0) {
                $this->lineTable->deleteBy(["id_quotations" => $idQuotation]);
            }
            foreach ($lines as $line) {
                $line["id_quotations"] = (int)$idQuotation;
                $line["unity_price"] = (float)$line["unity_price"];
                $line["id_unity_type"] = (int)$line["id_unity_type"];
                $line["unity"] = (int)$line["unity"];
                if (!$this->lineTable->insert($line)) {
                    dd("ici");
                    $this->db->rollBack();
                    return false;
                }
            }
            $idAcompte = $this->paymentsTypeTable->findBy(["slug" => "acompte"])->id;
            $this->paymentsTable->deleteBy(["id_quotations" => $idQuotation]);
            foreach ($acomptes as $acompte) {
                $acompte["id_quotations"] = $idQuotation;
                $acompte["id_payments_type"] = $idAcompte;
                $this->paymentsTable->insert($acompte);
            }
            $this->deleteKeys("id_status", $params);
            $this->quotationTable->update($idQuotation, $params);
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            dd($e->getMessage());
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * @param int $idQuotation
     * @param int $idStatus
     * @return bool
     */
    public
    function statusExist(int $idQuotation, int $idStatus): bool
    {
        try {
            $this->quotationStatusTable->findBy(["id_status" => $idStatus, "id_quotations" => $idQuotation]);
            return true;
        } catch (NoRecordException $e) {
            return false;
        }
    }

    /**
     * @param int $idUsers
     * @param int $idQuotation
     * @return mixed
     * @internal param int $idQuotations
     */
    public
    function getStatusDevis(int $idUsers, int $idQuotation)
    {
        $query = $this->container->get(Query::class);
        $idQuotation = $this->quotationTable->findBy(["id" => $idQuotation, "id_users" => $idUsers])->id;
        $results = $query->query("SELECT id_status as id 
                                        FROM quotation_status 
                                        WHERE id_quotations=:id AND updated_at IN (SELECT max(updated_at) FROM quotation_status
                                                                                  WHERE id_quotations=:id)", ["id" => $idQuotation]);
        $idStatus = $results["content"][0]->id;
        return $this->statusTable->find($idStatus);
    }

    /**
     * @param int $idUsers
     * @param int $idQuotation
     * @return bool
     */
    public
    function peutModifierLeDevis(int $idUsers, int $idQuotation): bool
    {
        $status = $this->getStatusDevis($idUsers, $idQuotation);
        return $status->slug == self::EN_COURS_DE_SAISIE || $status->slug == self::EN_POURPARLER || $status->slug == self::SAISIE_TERMINEE;
    }

    public
    function getStatusListe()
    {
        return $this->statusTable->findAll()->fetchAll();
    }

    public
    function historiqueModificationDevis(int $idUsers, int $idQuotation)
    {
        $idQuotation = $this->quotationTable->findBy(["id_users" => $idUsers, "id" => $idQuotation])->id;
        $query = $this->container->get(Query::class);
        return $query->query("SELECT qs.updated_at, name from 
                                          quotation_status qs inner join status s on s.id=qs.id_status 
                                          WHERE id_quotations=:id ORDER BY updated_at ASC;", ["id" => $idQuotation]);
    }

    public
    function getClientDevis(int $idUsers, string $slug)
    {

    }

    /**
     * @param int $idUsers
     * @param string $slug
     * @return bool|mixed
     */
    public
    function getDevisByIdUsersAndSlug(int $idUsers, string $slug)
    {
        return $this->quotationTable->findBy([
            "id_users" => $idUsers,
            "slug" => $slug
        ]);
    }

    /**
     * @param int $idUsers
     * @param string $slug
     * @return mixed
     */
    public
    function getClientFromDevis(int $idUsers, string $slug)
    {
        $devis = $this->quotationTable->findBy([
            "id_users" => $idUsers,
            "slug" => $slug
        ]);
        if (!is_null($devis->idProfessionnal)) {
            return $this->professionnalTable->find($devis->idProfessionnal);
        } else {
            //idIndividual
            return $this->individualTable->find($devis->idIndividual);
        }
    }

    /**
     * Retourne le montant du devis en fonction de l'id du devis et de l'id de l'utilisateur
     * @param int $idUsers
     * @param string $slugQuotation
     * @param bool|null $doRemise
     * @return float
     * @internal param bool|null $remise
     * @internal param int $idQuotation
     */
    public
    function getMontantDevis(int $idUsers, string $slugQuotation, ?bool $doRemise = true): float
    {
        $remise = 0.0;
        $prixTotal = 0.0;
        $quotation = $this->quotationTable->findBy([
            "id_users" => $idUsers,
            "slug" => $slugQuotation
        ]);
        $lines = $this->lineTable->findAll([
            "id_quotations" => (int)$quotation->id
        ])->fetchAll();
        foreach ($lines as $line) {
            $unityType = $this->unityTable->find($line->idUnityType);
            if ($unityType->slug === "remise") {
                $remise += (float)$line->unityPrice;
            } else {
                $prixTotal += (float)($line->unity * $line->unityPrice);
            }
        }
        if ($doRemise) {
            return self::getPrixAvecRemise($prixTotal, $remise);
        } else {
            return $prixTotal;
        }
    }

    public
    function getPayments(int $idUsers, int $idQuotation)
    {
        $quotation = $this->quotationTable->findBy([
            "id_users" => $idUsers,
            "id" => $idQuotation
        ]);
        $paymentsType = $this->paymentsTypeTable->findBy(["slug" => "acompte"]);
        $payments = $this->paymentsTable->findAll(["id_payments_type" => $paymentsType->id, "id_quotations" => $idQuotation])->fetchAll();
        $total = $this->getMontantDevis($idUsers, $quotation->slug);
        $details = [];
        foreach ($payments as $payment) {
            $payment = (array)$payment;
            $payment["subTotal"] = ($payment["amount"] / 100) * $total;
            $details[] = $payment;
        }
        return $details;
    }


    public
    function getDetailsCalculMontantDuDevis(int $idUsers, string $slugQuotation): array
    {
        $prixTotal = $this->getMontantDevis($idUsers, $slugQuotation, false);
        $detail = [];
        $quotation = $this->quotationTable->findBy([
            "id_users" => $idUsers,
            "slug" => $slugQuotation
        ]);
        $lines = $this->lineTable->findAll([
            "id_quotations" => (int)$quotation->id
        ])->fetchAll();
        foreach ($lines as $line) {
            $unityType = $this->unityTable->find($line->idUnityType);
            if ($unityType->slug === "remise") {
                $lineArray = (array)$line;
                $subTotal = $prixTotal * ($line->unityPrice / 100);
                $lineArray["subTotal"] = floor((float)$subTotal);
                $detail[] = $lineArray;
            } else {
                $lineArray = (array)$line;
                $lineArray["subTotal"] = (float)($line->unity * $line->unityPrice);
                $detail[] = $lineArray;
            }
        }
        return $detail;
    }


    public
    static function getPrixAvecRemise(float $prix, float $remise): float
    {
        if ($remise > 0) {
            return $prix - ($prix * ($remise / 100));
        }
        return $prix;
    }

    /**
     * @param int $idUsers
     * @return array
     */
    public
    function getListDevis(int $idUsers): array
    {

        $statement = "SELECT quotations.slug, quotations.id as idQ, id_professionnal, id_individual, quotations.updated_at as modif, object, 
      CASE WHEN quotations.id_individual IS NOT NULL THEN 'individual' ELSE 'professionnal' END as 'status' from quotations where id_users=:id";
        $query = $this->container->get(Query::class);
        $results = $this->mergeQuotationsClient($idUsers, $query->query($statement, ["id" => $idUsers]));
        for ($i = 0; $i < $results["nb"]; $i++) {
            $results["content"][$i]->modif = new \DateTime($results["content"][$i]->modif);
            $results["content"][$i]->prix = $this->getMontantDevis($idUsers, $results["content"][$i]->slug);
            $results["content"][$i]->position = $this->getStatusDevis($idUsers, $results["content"][$i]->idQ)->name;
        }
        return $results;
    }

    /**
     * @param int $idUsers
     * @param array $quotations
     * @return array
     */
    private
    function mergeQuotationsClient(int $idUsers, array $quotations): array
    {
        if ($quotations["nb"] > 0) {
            for ($i = 0; $i < $quotations["nb"]; $i++) {
                $temporary = (array)$quotations["content"][$i];
                if ($temporary["status"] == "individual") {
                    $client = $this->individualTable;
                    $id = $temporary["id_individual"];
                } else {
                    $client = $this->professionnalTable;
                    $id = $temporary["id_professionnal"];
                }
                $data = (array)$client->find($id);
                $temporary = array_merge($temporary, $data);
                $quotations["content"][$i] = (object)$temporary;
            }
        }
        return $quotations;
    }

    public
    function getUnities()
    {
        return $this->unityTable->findAll()->fetchAll();
    }

    public
    function createQuotation(int $userId, array $params): bool
    {
        $this->db->beginTransaction();
        try {
            $this->addKey("id_users", $userId, $params);
            list($type, $idClient) = explode("-", $params["client"]);
            $this->deleteKeys("client", $params);
            if ($type == "particulier") {
                $this->addKey("id_individual", $idClient, $params);
            } else {
                $this->addKey("id_professionnal", $idClient, $params);
            }
            $this->notRequired("join", $params);
            $this->notRequired("deadline", $params);
            $lines = $params["line"];
            $this->deleteKeys("line", $params);
            $this->setCreatedAt($params);
            $acomptes = [];
            if (isset($params["acompte"])) {
                $acomptes = $params["acompte"];
                $this->deleteKeys("acompte", $params);
            }

            if ($this->quotationTable->insert($params)) {
                $idQuotation = $this->db->lastInsertId();
                $year = substr(date('Y'), 2, 3);
                $slugUpdate = ["slug" => "$idQuotation{$year}"];
                $this->quotationTable->update($idQuotation, $slugUpdate);
                $this->addKey("slug", uniqid($userId), $params);
                $quotationStatus = [];
                $this->setCreatedAt($quotationStatus);
                $idStatus = $this->statusTable->findBy(["slug" => "en-cours-de-saisie"])->id;
                $quotationStatus["id_quotations"] = $idQuotation;
                $quotationStatus["id_status"] = $idStatus;
                if (!$this->quotationStatusTable->insert($quotationStatus)) {
                    throw new \Exception();
                }
                foreach ($lines as $line) {
                    $line["id_quotations"] = (int)$idQuotation;
                    $line["unity_price"] = (float)$line["unity_price"];
                    $line["id_unity_type"] = (int)$line["id_unity_type"];
                    $line["unity"] = (int)$line["unity"];
                    if (!$this->lineTable->insert($line)) {
                        $this->db->rollBack();
                        return false;
                    }
                }
                $idAcompte = $this->paymentsTypeTable->findBy(["slug" => "acompte"])->id;
                foreach ($acomptes as $acompte) {
                    $acompte["id_quotations"] = $idQuotation;
                    $acompte["id_payments_type"] = $idAcompte;
                    if (!$this->paymentsTable->insert($acompte)) {
                        $this->db->rollBack();
                        return false;
                    }
                }
                $this->db->commit();
                return true;
            }
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
        $this->db->rollBack();
        return false;
    }

    private function countNbDevis(int $idUsers): int
    {
        $devis = $this->quotationTable->findAll(["id_users" => $idUsers])->fetchAll();
        return count($devis) + 1;
    }

}