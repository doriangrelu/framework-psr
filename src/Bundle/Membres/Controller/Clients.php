<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 15/12/2017
 * Time: 12:47
 */

namespace App\Bundle\Membres\Controller;


use App\Bundle\Auth\Model\Connexion;
use App\Bundle\Membres\MembresBundle;
use Framework\Database\NoRecordException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Bundle\Membres\Model\Client as ClientModel;

class Clients extends MembresBundle
{
    /**
     * @var ClientModel
     */
    private $clientModel;
    /**
     * @var Connexion
     */
    private $connexionModel;
    /**
     * @param ServerRequestInterface $request
     * @param ContainerInterface $container
     */
    public function initialize(ServerRequestInterface $request, ContainerInterface $container)
    {
        parent::initialize($request, $container); // TODO: Change the autogenerated stub
        $this->clientModel = $this->loadModel(ClientModel::class);
        $this->connexionModel = $this->loadModel(Connexion::class);
    }

    /**
     * @param string $type
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function modifier(string $type, int $id)
    {
        if ($this->clientModel->clientAppartientA($this->usersId, $id, $type)) {
            if ($this->clientModel->modifierClient($id, $this->parsedBody, $type)) {
                $this->flash->success("Modifications appliquées avec succès !");
            } else {
                $this->flash->warning("Attention, le client n'a pas pu être modifié");
            }
        } else {
            $this->flash->error("Le client que vous tentez de modifier n'est pas dans votre base de données");
            return $this->redirect("membre.clients");
        }
        return $this->redirect("membre.client.afficher", ["type" => $type, "id" => $id]);
    }

    /**
     * @param string $type
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function modifierType(string $type, int $id)
    {
        $redirectType = "particulier";
        if ($type == (string)"particulier") {
            $redirectType = "professionnel";
        }

        if ($this->clientModel->clientAppartientA($this->usersId, $id, $type)) {
            if ($redirectType == "professionnel") {
                $validator = $this->getValidator();
                $validator->siret("siret")->notEmpty("compagny_name");
                if (!$validator->isValid()) {
                    $this->flash->error("Le client n'a pas été modifié");
                    return $this->redirect("membre.client.afficher", ["type" => $type, "id" => $id]);
                }
            }
            $newId = $this->clientModel->modifierType($id, $type, $this->parsedBody);
            if (!is_null($newId)) {
                $this->flash->success("Client modifié avec succès !");
            } else {
                $this->flash->error("Le client n'a pas été modifié");
            }
        } else {
            $this->flash->error("Le client que vous tentez de mofier n'exist pas");
            return $this->redirect("membre.clients");
        }
        return $this->redirect("membre.client.afficher", ["type" => $redirectType, "id" => $newId]);
    }

    /**
     * @param string $type
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     */
    public
    function supprimer(string $type, int $id)
    {
        if ($this->clientModel->clientAppartientA($this->usersId, $id, $type)) {
            if ($this->clientModel->supprimerClient($id, $type)) {
                $this->flash->success("Client supprimé succès !");
            } else {
                $this->flash->warning("Attention, le client n'a pas pu être modifié");
            }
        } else {
            $this->flash->error("Le client que vous tentez de modifier n'est pas dans votre base de données");
        }
        return $this->redirect("membre.clients");
    }

    /**
     * @param string $type
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public
    function afficher(string $type, int $id)
    {

        try {
            $this->renderer->setActive("bdd");
            $this->renderer->setActive("clients", 2);
            $this->renderer->make([
                "values" => $this->clientModel->getClient($this->usersId, $id, $type)
            ]);
            return $this->renderer->render("Client");
        } catch (NoRecordException $e) {
            $this->flash->warning("Le client n'existe pas");
            return $this->redirect("membre.clients");
        }
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public
    function ajouter()
    {
        $validator = $this->getValidator();
        $validator->required("first_name", "last_name");
        if ($this->parsedBody["clientType"] == "pro") {
            $validator->siret("siret")
                ->required("compagny_name");
        }
        if ($validator->isValid()) {
            if ($this->clientModel->ajouter($this->usersId, $this->parsedBody)) {
                $this->flash->success("Client ajouté avec succès !");
            } else {
                $this->flash->error("Le formulaire comporte des erreurs");
                $this->setErrors($validator, $this->parsedBody);
                $this->makeErrors();
            }
        } else {
            $this->flash->error("Le formulaire comporte des erreurs");
            $this->setErrors($validator, $this->parsedBody);
            $this->makeErrors();
        }
        return $this->redirect("membre.clients");
    }

    public function vider()
    {
        if(isset($this->parsedBody["password"])){
            if($this->connexionModel->checkLoginPassword($this->connexionModel->userIsConnected()->getUsername(), $this->parsedBody["password"])){
                if($this->clientModel->viderBaseDeDonnees($this->usersId)){
                    $this->flash->success("Base de données vidée avec succès !");
                    return $this->redirect("membre.clients");
                }
                $this->flash->error("La base de données n'a pas été vidée, si cette erreur persiste contactez votre administrateur.");
                return $this->redirect("membre.clients");
            }
        }
        $this->flash->error("Une erreur est survenue, la supression de la base de données client a échouée, le mot de passe est incorrect");
        return $this->redirect("membre.clients");
    }

    public function getListeProfessionnel()
    {
        return $this->clientModel->getListeParticulier($this->usersId)->getRecordsJSON();
    }

    public function getListeParticulier()
    {
        return $this->clientModel->getListeProfessionnel($this->usersId);
    }

    /**
     * Importe une base de données client au format CSV
     * @return string
     */
    public function importer()
    {

        return "";
    }


}