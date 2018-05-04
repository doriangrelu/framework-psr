<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 06/12/2017
 * Time: 15:01
 */

namespace Framework\Validator;

trait Rules
{
    /**
     * Vérifie que les champs sont présents dans le tableau
     *
     * @param string[] ...$keys
     * @return self
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }

    /**
     * @param string[] ...$keys
     * @return self
     */
    public function siret(string ...$keys): self
    {
        foreach ($keys as $key) {
            if (!preg_match("/[0-9]{14,15}/", $this->getValue($key))) {
                $this->addError($key, 'siret');
            }
        }
        return $this;
    }

    /**
     * @param string[] ...$keys
     * @return self
     */
    public function codePostal(string ... $keys): self
    {
        foreach ($keys as $key) {
            if (!preg_match("/[0-9]{5}/", $this->getValue($key))) {
                $this->addError($key, "codePostal");
            }
        }
        return $this;
    }

    /**
     * @param string[] ...$keys
     * @return self
     */
    public function phoneNumber(string ...$keys): self
    {
        $pattern = "(0[0-9]([ .-]?[0-9]{2}){4})";
        foreach ($keys as $key) {
            if (!preg_match($pattern, $this->getValue($key))) {
                $this->addError($key, "phone");
            }
        }
        return $this;
    }
    /**
     * Vérifie que le champs n'est pas vide
     *
     * @param string[] ...$keys
     * @return self
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value)) {
                $this->addError($key, 'empty');
            }
        }
        return $this;
    }

    /**
     * @param string $key
     * @param int|null $min
     * @param int|null $max
     * @return self
     */
    public function length(string $key, ?int $min, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (!is_null($min) &&
            !is_null($max) &&
            ($length < $min || $length > $max)
        ) {
            $this->addError($key, 'betweenLength', [$min, $max]);
            return $this;
        }
        if (!is_null($min) &&
            $length < $min
        ) {
            $this->addError($key, 'minLength', [$min]);
            return $this;
        }
        if (!is_null($max) &&
            $length > $max
        ) {
            $this->addError($key, 'maxLength', [$max]);
        }
        return $this;
    }

    /**
     * Vérifie que l'élément est un slug
     *
     * @param string $key
     * @return self
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }

    /**
     * Vérifie qu'une date correspond au format demandé
     *
     * @param string $key
     * @param string $format
     * @return self
     */
    public function dateTime(string $key, string $format = "Y-m-d H:i:s"): self
    {
        $value = $this->getValue($key);
        $date = \DateTime::createFromFormat($format, $value);
        $errors = \DateTime::getLastErrors();
        if ($errors['error_count'] > 0 || $errors['warning_count'] > 0 || $date === false) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }

    /**
     * Vérifie que la clef existe dans la table donnée
     *
     * @param string $key
     * @param string $table
     * @param \PDO $pdo
     * @return self
     */
    public function exists(string $key, string $table, \PDO $pdo): self
    {
        $value = $this->getValue($key);
        $statement = $pdo->prepare("SELECT id FROM $table WHERE id = ?");
        $statement->execute([$value]);
        if ($statement->fetchColumn() === false) {
            $this->addError($key, 'exists', [$table]);
        }
        return $this;
    }

    /**
     * Vérifie que la clef est unique dans la base de donnée
     *
     * @param string $key
     * @param string $table
     * @param \PDO $pdo
     * @param int|null $exclude
     * @return self
     */
    public function unique(string $key, string $table, \PDO $pdo, ?int $exclude = null, ?string $field = "id"): self
    {
        $value = $this->getValue($key);
        $query = "SELECT $field FROM $table WHERE $key = ?";
        $params = [$value];
        if ($exclude !== null) {
            $query .= " AND $field != ?";
            $params[] = $exclude;
        }
        $statement = $pdo->prepare($query);
        $statement->execute($params);
        if ($statement->fetchColumn() !== false) {
            $this->addError($key, 'unique', [$value]);
        }
        return $this;
    }

    /**
     * @param string $key
     * @return self
     */
    public function mail(string $key): self
    {
        $value = $this->getValue($key);
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($key, "mail", [$value]);
        }
        return $this;
    }


}