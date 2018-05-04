<?php

namespace Framework\Validator;

class ValidationError
{

    private $key;
    private $rule;
    private $messages = [
        'required' => 'Le champs %s est requis',
        'empty' => 'Le champs %s ne peut être vide',
        'slug' => 'Le champs %s n\'est pas un slug valide',
        'minLength' => 'Le champs %s doit contenir plus de %d caractères',
        'maxLength' => 'Le champs %s doit contenir moins de %d caractères',
        'betweenLength' => 'Le champs %s doit contenir entre %d et %d caractères',
        'datetime' => 'Le champs %s doit être une date valide',
        'exists' => 'Le champs %s n\'existe pas sur dans la table %s',
        'unique' => 'La valeur du champs %s est déjà utilisée, veuillez la changer',
        "mail" => 'Le champs %s doit être une adresse mail',
        "codePostal" => 'Le champs %s doit être un code postal',
        "phone" => 'Le champs %s doit être un numéro de téléphone',
        "siret" => 'Le champs %s doit être un numéro siret'
    ];
    /**
     * @var array
     */
    private $attributes;

    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $traductor = new FieldTraduction();
        $this->key = "<b>{$traductor->getFrenchName($key)}</b>";
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    public function __toString()
    {
        $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
        return (string)call_user_func_array('sprintf', $params);
    }
}
