<?php

use Framework\Mode;

return [

    /**
     * App Configuration
     */
    "app" => [
        "name" => "Application Name Here",
        "mode" => Mode::DEVELOPPEMENT,
        "auth" => [
            "userTable" => "",
            "rolesTable" => "",
            "tokenSecurity" => true
        ],
        "seed" => dirname(__DIR__) . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . "Database" . DIRECTORY_SEPARATOR . "Seed" . DIRECTORY_SEPARATOR
    ],

    /**
     * Auth Configuration
     */
    "Auth" => [
        "Auth.actived"=>true, // Active module
        "Auth.Exception" => false, //Exception if is forbidden
        "Auth.route.redirectLogin" => "", // If forbidden route name redirection to login page
        "Auth.user" => "", //Implemention of App\Framework\Auth\UserInterface
    ],

    /**
     * Database définition
     */
    "database" => [
        'database.host' => 'localhost',
        'database.username' => 'root',
        'database.password' => '',
        'database.name' => 'doriangrelu'
    ],

    /**
     * Mail Configuration
     */
    "mailer" => [
        'mail.host' => "localhost",
        'mail.port' => 1025,
        'mail.username' => null,
        'mail.password' => null,
        'mail.from' => ["doriangrelu@gmail.com" => "Dorian GRELU"]
    ],

];