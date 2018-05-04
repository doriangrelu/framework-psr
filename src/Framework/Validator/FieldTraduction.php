<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 04/12/2017
 * Time: 11:51
 */

namespace Framework\Validator;

use Cake\Utility\Inflector;

class FieldTraduction
{
    private $fields=[
        "first_name"=>"prénom",
        "last_name"=>"nom",
        "birthday"=>"date de naissance",
        "city"=>"commune",
        "cp"=>"code postal",
        "phone"=>"numéro de téléphone",
        "phone_number"=>"numéro de téléphone",
        "adress"=>"adresse",
        "adress_complement"=>"complément d'adresse",
        "siret"=>"numéro SIRET",
        "compagny_name"=>"raison sociale",
        "password"=>"mot de passe",
        "object"=>"Objet",
        "validity_deadline"=>"Date de fin de validité de l'offre",
        "deadline"=>"Date de fin du projet",
        "join"=>"Texte à adjoindre au devis"
    ];

    /**
     * FieldTraduction constructor.
     * @param array|null $fields
     */
    public function __construct(?array $fields=[])
    {
        $this->fields=array_merge($this->fields, $fields);
    }

    /**
     * @param string $name
     * @return null|string
     */
    public function getFrenchName(string $name):?string
    {
        $name=Inflector::underscore($name);
        return $this->fields[$name] ?? $name;
    }
}