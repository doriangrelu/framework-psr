<?php

use Framework\Mode;

/**
 * Created by PhpStorm.
 * User: doria
 * Date: 01/12/2017
 * Time: 20:33
 */

class ModeTest extends \PHPUnit\Framework\TestCase
{
    public function testMode()
    {
        Mode::init(Mode::DEVELOPPEMENT);
        $this->assertTrue(Mode::is_dev());
        Mode::setMode(Mode::PRODUCTION);
        $this->assertTrue(Mode::is_prod());
    }

    public function testIsCli()
    {
        $this->assertTrue(Mode::is_cli());
    }
}