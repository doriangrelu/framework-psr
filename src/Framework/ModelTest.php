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
use App\Framework\ORM\Entity;
use App\Framework\ORM\InsertQuery;
use App\Framework\ORM\SelectQuery;
use App\Framework\ORM\UpdateQuery;
use Framework\Database\NoRecordException;

class ModelTest
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
        return new ModelTest(Container::get(\PDO::class), $table);
    }


    /**
     * Select element
     * @param string ...$fields
     * @return SelectQuery
     */
    public function select(string...$fields): SelectQuery
    {
        $this->lastAction = self::LOD;
        if (count($fields) === 0) {
            $fields = ["{$this->table}.*"];
        }
        return (new SelectQuery($this->pdo, $this->table, $fields, $this->entity));
    }

    public function insert(array $values): InsertQuery
    {
        $this->lastAction = self::MOD;
        return (new InsertQuery($this->pdo, $this->table, $values));
    }

    public function update(array $values): UpdateQuery
    {
        $this->lastAction = self::MOD;
        return (new UpdateQuery($this->pdo, $this->table, $values));
    }

    /**
     * @throws UnsupportedOperationException
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
                return $this->update($this->fields)->where(implode(" AND ", $primaries))->save();
            } catch (NoRecordException $e) {
                return $this->insert($this->fields)->save();
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
}