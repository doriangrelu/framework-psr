<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/05/2018
 * Time: 18:57
 */

namespace Framework;


use App\Framework\Exception\ORMException;
use App\Framework\Exception\UnsupportedOperationException;
use App\Framework\Facades\Container;
use App\Framework\ORM\DeleteQuery;
use App\Framework\ORM\Entity;
use App\Framework\ORM\InsertQuery;
use App\Framework\ORM\SelectQuery;
use App\Framework\ORM\UpdateQuery;
use Framework\Database\NoRecordException;

class Model
{

    private const MOD = 1;
    private const LOD = 2;

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

    protected $fillableSecurity = true;

    /**
     * Dernière action effectuée par le framework
     * @var String|null
     */
    private $lastAction = null;

    public function __get($name)
    {
        return $this->fields[$name] ?? null;
    }

    public function __set($name, $value)
    {
        if ($this->fieldIsPrimary($name) && isset($this->fields[$name]) && $this->fields[$name] !== null) {
            $this->update([$name => $value])->where("`$name`=" . addslashes($this->fields[$name]))->save();
        }
        if(!$value instanceof \DateTime && ($name === "createdAt" or $name === "updatedAt")){
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
    }

    public static function create(string $table): self
    {
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

    public function delete(): DeleteQuery
    {
        return (new DeleteQuery($this->pdo, $this->table));
    }

    public function insert(array $values): InsertQuery
    {
        $values = $this->executeGuard($values);
        return (new InsertQuery($this->pdo, $this->table, $values));
    }

    public function update(array $values): UpdateQuery
    {
        $values = $this->executeGuard($values);
        return (new UpdateQuery($this->pdo, $this->table, $values));
    }

    /**
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

    public function setCreatedAt(string $date){
        $this->__set("createdAt", new \DateTime($date));
    }

    public function setUpdatedAt(string $date){
        $this->__set("setUpdatedAt", new \DateTime($date));
    }

}