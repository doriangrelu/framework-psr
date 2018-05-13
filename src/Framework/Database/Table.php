<?php

namespace Framework\Database;

use App\Framework\Event\Emitter;
use Framework\App;
use Pagerfanta\Pagerfanta;

class Table
{

    /**
     * @var null|\PDO
     */
    protected $pdo;

    /**
     * Nom de la table en BDD
     * @var string
     */
    protected $table;

    /**
     * Entité à utiliser
     * @var string
     */
    protected $entity = \stdClass::class;

    /**
     * @var Emitter
     */
    private $emitter;

    private $properties = [];


    public function __construct(\PDO $pdo, Emitter $emitter)
    {
        $this->pdo = $pdo;
        $this->emitter = $emitter;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        }
        return null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * Récupère une liste clef valeur de nos enregistrements
     */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(\PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    /**
     * @return Query
     */
    public function makeQuery(): Query
    {
        return (new Query($this->pdo))
            ->from($this->table, $this->table[0])
            ->into($this->entity);
    }

    /**
     * @param array $fields
     * @return Query
     */
    public function findAll(array $fields = []): Query
    {
        if (count($fields) > 0) {
            $i = 0;
            $field = "";
            foreach ($fields as $key => $value) {
                $field .= "`$key`=:$key";
                $i++;
                if ($i < count($fields)) {
                    $field .= " AND ";
                }
            }
            return $this->makeQuery()->where("$field")->params($fields);
        }

        return $this->makeQuery();
    }

    /**
     * Récupère une ligne par rapport à un champs
     * @param $fields
     * @param array $value
     * @return bool|mixed
     * @throws NoRecordException
     */
    public function findBy($fields, $value = [])
    {
        $field = "";
        $i = 0;
        if (is_array($fields)) {
            foreach ($fields as $key => $value) {
                $field .= "`$key`=:$key";
                $i++;
                if ($i < count($fields)) {
                    $field .= " AND ";
                }
            }
            return $this->makeQuery()->where("$field")->params($fields)->fetchOrFail();
        } else {
            $field = $fields;
            return $this->makeQuery()->where("$field = :field")->params(["field" => $value])->fetchOrFail();
        }
    }

    /**
     * Récupère un élément à partir de son ID
     *
     * @param int $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        return $this->makeQuery()->where("id = $id")->fetchOrFail();
    }

    /**
     * Récupère le nbre d'enregistrement
     *
     * @return int
     */
    public function count(): int
    {
        return $this->makeQuery()->count();
    }

    /**
     * Met à jour un enregistrement au niveau de la base de données
     *
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $this->emit("update");
        $fieldQuery = $this->buildFieldQuery($params);
        $params["id"] = $id;
        $query = $this->pdo->prepare("UPDATE `{$this->table}` SET $fieldQuery WHERE id = :id");
        return $query->execute($params);
    }


    /**
     * @param array $fields
     * @return array
     */
    private function escapeFieldsName(array $fields): array
    {
        return array_map(function ($field) {
            return "`$field`";
        }, $fields);
    }

    /**
     * Crée un nouvel enregistrement
     *
     * @param array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $this->emit("insert");
        $fields = array_keys($params);
        $values = join(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = $this->escapeFieldsName($fields);
        $fields = join(', ', $fields);
        $query = $this->pdo->prepare("INSERT INTO `{$this->table}` ($fields) VALUES ($values)");
        return $query->execute($params);
    }



    /**
     * Retourne le dernièr ID inséré
     * @param null|string $name
     * @return string
     */
    public function lastInsertId(?string $name = null): string
    {
        return $this->pdo->lastInsertId($name);
    }

    /**
     * Supprime un enregistrment
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->emit("delete");
        $query = $this->pdo->prepare("DELETE FROM `{$this->table}` WHERE id = ?");
        return $query->execute([$id]);
    }

    public function deleteBy(?array $cond = [])
    {
        $this->emit("delete");
        $condition = "";
        $conds = [];
        $values = [];
        foreach ($cond as $key => $value) {
            $conds[] = "`$key`=:$key";
        }
        if (count($conds) > 0) {
            $condition = "WHERE ";
            $condition .= join(" AND ", $conds);
        }
        $query = $this->pdo->prepare("DELETE FROM `{$this->table}` $condition");
        return $query->execute($cond);

    }

    private function buildFieldQuery(array $params)
    {
        return join(', ', array_map(function ($field) {
            return "`$field` = :$field";
        }, array_keys($params)));
    }

    /**
     * @param string $eventType
     */
    private function emit(string $eventType): void
    {
        $this->emitter->emit("on.{$this->table}.$eventType");
    }

    /**
     * @return mixed
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Vérifie qu'un enregistrement existe
     * @param $id
     * @return bool
     */
    public function exists($id): bool
    {
        $query = $this->pdo->prepare("SELECT id FROM `{$this->table}` WHERE id = ?");
        $query->execute([$id]);
        return $query->fetchColumn() !== false;
    }

    /**
     * Retourne l'instance de PDO
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Lance une transaction
     */
    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    /**
     * Valide une transaction
     */
    public function commit(): void
    {
        $this->pdo->commit();
    }

    /**
     * Annule une transaction
     */
    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }

}
