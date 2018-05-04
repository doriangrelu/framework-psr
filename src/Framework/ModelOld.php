<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 06/12/2017
 * Time: 15:25
 */

namespace Framework;


use Framework\Database\Query;
use Framework\Database\TableLoader;
use Framework\Utility\ModelUtility;
use Framework\Utility\Utility;
use Psr\Container\ContainerInterface;

class ModelOld
{
    use Utility;
    use ModelUtility;
    /**
     * @var TableLoader
     */
    protected $table;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \PDO
     */
    protected $db;

    /**
     * Model constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->table = $container->get(TableLoader::class);
        $this->query = $container->get(Query::class);
        $this->setProperties($container);
        $this->container = $container;
        $this->db = $container->get(\PDO::class);
    }


}