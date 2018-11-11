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
    private $_pattern = "#^(make:)([controller]+|[model]+|[migration]+|[fixture]+)[\s]{1}([\w\-\/]{2,})$#i";
    private $_command;
    private $_matchesGroup = [];
    private $_app;

    public function __construct(App $app, array $argv)
    {
        $this->_command = trim(str_replace("console", "", implode(" ", $argv)));
        $this->_command = trim(str_replace("bin/", "", $this->_command));
        $this->_command = trim(str_replace("bin".DIRECTORY_SEPARATOR, "", $this->_command));
        $this->_app = $app;
    }


    /**
     * @throws ConsoleParserException
     */
    public function run()
    {
        if (preg_match($this->_pattern, $this->_command, $this->_matchesGroup) !== false) {
            if (count($this->_matchesGroup) > 0) {
                $commands = new Commands($this->_app, $this->_matchesGroup);
                $commands->run();
                return true;
            }
        }
        throw new ConsoleParserException("Try php console make:controller HomeController");
    }

}