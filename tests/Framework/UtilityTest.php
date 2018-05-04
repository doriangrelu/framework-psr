<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 30/11/17
 * Time: 16:31
 */

use Framework\Router;


class TestUtilityClass extends \PHPUnit\Framework\TestCase
{

    /**
     * @var \Framework\Utility\Objects\Utility
     */
    protected $utility;

    public function setUp()
    {
        $this->utility = new \Framework\Utility\Objects\Utility();
    }

    public function testGetToken()
    {
        $this->assertNotEmpty($this->utility->generateToken());
    }

    public function testGenerateRandomString()
    {
        $len = strlen($this->utility->randomString(10, true, true));
        $this->assertEquals(10, $len);
    }


}