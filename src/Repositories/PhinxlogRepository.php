<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 05/06/2018
 * Time: 14:19
 */

namespace App\Repositories;

use Doctrine\ORM\EntityRepository;

class PhinxlogRepository extends EntityRepository
{
    public function getTest()
    {
        return 1;
    }
}