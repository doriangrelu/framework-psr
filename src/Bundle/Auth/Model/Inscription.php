<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 06/12/2017
 * Time: 15:18
 */

namespace App\Bundle\Auth\Model;

use App\Bundle\Database\Table\UsersTable;
use Framework\Model;
use Psr\Container\ContainerInterface;

class Inscription extends Model
{
    /**
     * @var UsersTable
     */
    private $userTable;

    /**
     * @param ContainerInterface $container
     * @internal param array $params
     */

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->userTable=$this->table->load(UsersTable::class);
    }

    public function signUp(array $params):bool
    {
        $this->setCreatedAt($params);
        $password=$this->password->hash($params["password"]);
        $this->updateKey("password", $password, $params);
        $this->addKey("id_roles", 1, $params);
        if($this->userTable->insert($params)){
            return true;
        }
        return false;
    }
}