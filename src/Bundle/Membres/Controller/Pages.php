<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 09/12/2017
 * Time: 11:11
 */

namespace App\Bundle\Membres\Controller;


use App\Bundle\Auth\Model\Connexion;
use App\Bundle\Membres\MembresBundle;
use App\Bundle\Membres\Model\Client;
use App\Bundle\Membres\Model\QuotationsModel;
use App\Bundle\Membres\Model\StatistiquesModel;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class Pages extends MembresBundle
{

    /**
     * @var \App\Bundle\Membres\Model\Informations
     */
    protected $informationModel;

    /**
     * @var StatistiquesModel
     */
    protected $statistiqueModel;

    /**
     * @var Connexion
     */
    protected $connexionModel;

    public function initialize(ServerRequestInterface $request, ContainerInterface $container)
    {
        parent::initialize($request, $container); // TODO: Change the autogenerated stub
        $this->informationModel = $this->loadModel(\App\Bundle\Membres\Model\Informations::class);
        $this->statistiqueModel = $this->loadModel(StatistiquesModel::class);
        $this->connexionModel = $this->loadModel(Connexion::class);
    }

    public function home()
    {
        $this->renderer->setActive("pannel");
        $this->setPage("Pannel d'administration");
        $this->renderer->make([
            "values" => $this->usersTable->find($this->session->get(Connexion::ID_SESSION)),
            "totp"=>$this->connexionModel->activedTOTP($this->usersId)
        ]);
        return $this->renderer->render("Home");
    }

    public function factures()
    {
        $quotationModel=$this->container->get(QuotationsModel::class);
        $this->renderer->setActive("factures", 2);
        $this->renderer->setActive("factures");
        $this->renderer->make([
            "devisListe"=>$quotationModel->getListDevis($this->usersId)
        ]);
        return $this->renderer->render("Factures");
    }

    public function clients()
    {
        $clientModel = $this->loadModel(Client::class);
        $this->renderer->make([
            "clients" => $clientModel->getListeClients($this->usersId)
        ]);
        $this->renderer->setActive("bdd");
        $this->renderer->setActive("clients", 2);
        return $this->renderer->render("clients");
    }

    public function nouveauDevis()
    {
        $this->renderer->setLayout("Simple");
        $clientModel=$this->container->get(Client::class);
        $quotationModel=$this->container->get(QuotationsModel::class);
        if(count($clientModel->getListeClients($this->usersId))>0){
            $this->renderer->make([
                "clients" => $clientModel->getListeClients($this->usersId),
                "pros"=>$clientModel->getListeProfessionnel($this->usersId),
                "parts"=>$clientModel->getListeParticulier($this->usersId),
                "unities" => $quotationModel->getUnities()
            ]);
            return $this->renderer->render("NouveauDevis");
        }
        $this->flash->error("Vous devez avoir au minimul un client pour ajouter un devis.");
        return $this->redirect("devis");
    }

    public function devis()
    {
        $clientModel=$this->container->get(Client::class);
        $quotationModel=$this->container->get(QuotationsModel::class);
        $this->renderer->make([
            "clients" => $clientModel->getListeClients($this->usersId),
            "pros"=>$clientModel->getListeProfessionnel($this->usersId),
            "parts"=>$clientModel->getListeParticulier($this->usersId),
            "unities" => $quotationModel->getUnities(),
            "devis"=>$quotationModel->getListDevis($this->usersId)
        ]);
        $this->renderer->setActive("factures");
        $this->renderer->setActive("devis", 2);
        return $this->renderer->render("devis");
    }

    public function prospects()
    {
        $this->renderer->setActive("bdd");
        $this->renderer->setActive("prospects", 2);
        return $this->renderer->render("Prospects");
    }

    public function getCsvNouveauxClients()
    {
        $file = ROOT . "public" . DS . "export" . DS . "$this->usersId.csv";
        if (is_file($file)) {
            unlink($file);
        }
        $fp = fopen($file, 'w+');
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));
        foreach ($this->informationModel->getNombreNouveauClientParMois($this->usersId) as $line) {

            fputcsv($fp, $line, ",");
        }
        fclose($fp);
        return new Response(200, [
            "Content-disposition" => "attachment; filename=statistique.csv",
            "Content-type" => ["Content-type: text/csv"]
        ], file_get_contents($file));
    }

    public function statistiques()
    {
        $this->renderer->make([
            "base" => $this->statistiqueModel->getStatistiques($this->usersId),
            "nouveaux" => $this->informationModel->getNombreNouveauClientPourUnmois(date("m"), $this->usersId),
            "stats" => json_encode($this->informationModel->getTableauCompletNombreUtilisateursDerniersMois($this->usersId)),
            "pro"=>$this->informationModel->getPercentPro($this->usersId),
            "part"=>$this->informationModel->getPercentPart($this->usersId)
        ]);
        $this->renderer->setActive("stats");
        $this->setPage("Statistiques");
        return $this->renderer->render("Statistiques");
    }
}

