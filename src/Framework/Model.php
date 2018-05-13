<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/05/2018
 * Time: 18:57
 */

namespace Framework;

use App\Framework\Exception\ORMException;
use App\Framework\Facades\Container;
use App\Framework\ORM\DeleteQuery;
use App\Framework\ORM\Entity;
use App\Framework\ORM\InsertQuery;
use App\Framework\ORM\SelectQuery;
use App\Framework\ORM\UpdateQuery;
use Framework\Database\NoRecordException;
use Framework\Database\QueryResult;

class Model
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var null|string
     */
    protected $table;

    /**
     * @var Entity
     */
    private $entity;

    /**
     * @var array
     */
    private $fields = [];
    /**
     * @var array
     */
    protected $id = ["id"];

    /**
     * @var array
     */
    protected $fillable = [];
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Security insert from array
     * @var bool
     */
    protected $fillableSecurity = true;


    public function __get($name)
    {
        return $this->fields[$name] ?? null;
    }

    public function __set($name, $value)
    {
        if ($this->fieldIsPrimary($name) && isset($this->fields[$name]) && $this->fields[$name] !== null) {
            $this->update([$name => $value])->where("`$name`=" . addslashes($this->fields[$name]))->save();
        }
        if (!$value instanceof \DateTime && ($name === "createdAt" or $name === "updatedAt")) {
            $value = new \DateTime($value);
        }
        $this->fields[$name] = $value;
    }

    /**
     * ModelTest constructor.
     * @param \PDO $pdo
     * @param string|null $table
     */
    public function __construct(\PDO $pdo, string $table = null)
    {
        $this->entity = $this;
        $this->pdo = $pdo;
        if (!is_null($table)) {
            $this->table = $table;
        }
        if (empty($this->table)) {
            $className = explode("\\", get_class($this));
            $this->table = strtolower(end($className));
        }

    }

    /**
     * @param string $table
     * @return Model
     */
    public static function create(string $table)
    {
        $className = "App\Models\{$table}";
        if (class_exists($className)) {
            return Container::get($className);
        }
        return new Model(Container::get(\PDO::class), $table);
    }

    /**
     * Get new Entity
     * @return Model
     */
    public function getEntity()
    {
        if (get_class($this) === get_class($this->entity)) {
            $entityPointer = get_class($this->entity);
            return new $entityPointer(Container::get(\PDO::class), $this->table);
        }
        return self::create($this->table);

    }

    /**
     * @return mixed|null|string
     */
    public function findFirst()
    {
        $query = $this->select()->fetchAll();
        if ($query->count() > 0) {
            return $query->get(0);
        }
        return null;
    }

    /**
     * @param array $conditions
     * @return QueryResult
     */
    public function findBy(array $conditions): QueryResult
    {
        $fields = [];

        foreach (array_keys($conditions) as $field) {
            $fields[] = "$field=:$field";
        }
        return $this->select()->where(implode(" AND ", $fields))->params($conditions)->fetchAll();
    }

    /**
     * @return string
     */
    public function lastInsertedId(): string
    {
        return $this->pdo->lastInsertId();
    }


    /**
     * Select element
     * @param string ...$fields
     * @return SelectQuery
     */
    public function select(string...$fields): SelectQuery
    {
        if (count($fields) === 0) {
            $fields = ["{$this->table}.*"];
        }
        return (new SelectQuery($this->pdo, $this->table, $fields, $this->getEntity()));
    }

    /**
     * @return DeleteQuery
     */
    public function delete(): DeleteQuery
    {
        return (new DeleteQuery($this->pdo, $this->table));
    }

    /**
     * @param array $values
     * @return InsertQuery
     * @throws ORMException
     */
    public function insert(array $values): InsertQuery
    {
        $values = $this->executeGuard($values);
        if (count($values) === 0) {
            throw new ORMException("Missing fields on insert");
        }
        return (new InsertQuery($this->pdo, $this->table, $values));
    }

    /**
     * @param array $values
     * @return UpdateQuery
     * @throws ORMException
     */
    public function update(array $values): UpdateQuery
    {
        $values = $this->executeGuard($values);
        if (count($values) === 0) {
            throw new ORMException("Missing fields on insert");
        }
        return (new UpdateQuery($this->pdo, $this->table, $values));
    }

    /**
     * Save the instance Datas
     * @return bool|int|string
     * @throws ORMException
     */
    public function save()
    {
        if (count($this->fields) > 0) {
            try {
                $primaries = $this->getPrimariesKeysWithValuesAssignement();
                if (count($primaries) === 0) {
                    throw new NoRecordException();
                }
                static::create($this->table)
                    ->select()
                    ->where(implode(" AND ", $primaries))
                    ->fetchOrFail();
                return (new UpdateQuery($this->pdo, $this->table, $this->fields))->where(implode(" AND ", $primaries))->save();
            } catch (NoRecordException $e) {
                return (new InsertQuery($this->pdo, $this->table, $this->fields))->save();
            }
        }
        throw new ORMException("Missing fields");
    }

    /**
     * @return array
     */
    private function getPrimariesKeysWithValuesAssignement(): array
    {
        $primaries = [];
        foreach ($this->fields as $field => $value) {
            if (in_array($field, $this->id)) {
                $primaries[] = "`$field`=$value";
            }
        }
        return $primaries;
    }

    /**
     * @param string $field
     * @return bool
     */
    private function fieldIsPrimary(string $field): bool
    {
        return in_array($field, $this->id);
    }

    /**
     * @param array $fields
     * @return array
     */
    private function executeGuard(array $fields)
    {
        $notGuardedFields = [];
        foreach ($fields as $field => $value) {
            if (!in_array($field, $this->guarded) && ($this->fillableSecurity && in_array($field, $this->fillable))) {
                $notGuardedFields[$field] = $value;
            }
        }
        return $notGuardedFields;
    }

    /**
     * For Hydrator
     * @param string $date
     */
    public function setCreatedAt(string $date)
    {
        $this->__set("createdAt", new \DateTime($date));
    }

    /**
     * For Hydrator
     * @param string $date
     */
    public function setUpdatedAt(string $date)
    {
        $this->__set("setUpdatedAt", new \DateTime($date));
    }

}