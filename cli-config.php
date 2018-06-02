<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once 'vendor/autoload.php';
try{
    $app = new \Framework\App();
}  catch (Exception $e){
    $e->getTraceAsString();
}

$entityManager = $app->getContainer()->get(\Doctrine\ORM\EntityManager::class);
return ConsoleRunner::createHelperSet($entityManager);