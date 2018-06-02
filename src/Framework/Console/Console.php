<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 13/05/2018
 * Time: 10:55
 */

namespace App\Framework\Console;


use Framework\App;

class Console
{
    private $pattern = "#^(make:)([controller]+|[model]+|[migration]+|[addSeed]+)[\s]{1}([\w\-\/]{2,})$#i";
    private $command;
    private $matchesGroup = [];
    private $app;

    public function __construct(App $app, array $argv)
    {
        $this->command = trim(str_replace("console", "", implode(" ", $argv)));
        $this->app = $app;
    }


    /**
     * @throws ConsoleParserException
     */
    public function run()
    {
        if (preg_match($this->pattern, $this->command, $this->matchesGroup) !== false) {
            if (count($this->matchesGroup) > 0) {
                $commands = new Commands($this->app, $this->matchesGroup);
                $commands->run();
                return true;
            }
        }
        throw new ConsoleParserException("Try php console make:controller HomeController");
    }

}