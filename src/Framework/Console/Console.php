<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 13/05/2018
 * Time: 10:55
 */

namespace App\Framework\Console;


class Console
{
    private $pattern = "#^(make:)([controller]+|[model]+|[migration]+)[\s]{1}([\w\-\/]{2,})$#i";
    private $command;
    private $matchesGroup = [];

    public function __construct(array $argv)
    {
        $this->command = trim(str_replace("console", "", implode(" ", $argv)));
    }

    /**
     * @throws ConsoleParserException
     */
    public function run()
    {
        if(preg_match($this->pattern, $this->command, $this->matchesGroup)!==false){
            if(count($this->matchesGroup)>0){
                $commands = new Commands($this->matchesGroup);
                return true;
            }
        }
        throw new ConsoleParserException("Try php console make:controller HomeController");
    }

}