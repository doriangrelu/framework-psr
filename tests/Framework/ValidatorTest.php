<?php

use Doctrine\ORM\EntityManagerInterface;
use Framework\App;
use Framework\Decorator\ValidatorDecorator;
use Framework\Validator\Validator;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/12/2018
 * Time: 14:07
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $app = new App();
    }

    public function testRequiredFields()
    {
        $request = (new ServerRequest('get', 'test'))->withParsedBody(['' => '']);
        $validator = new Validator(App::$containerForFacade->get(\Doctrine\ORM\EntityManager::class), $request);
        $decorator = new ValidatorDecorator($validator);
        $decorator->required('name');
        $this->assertFalse($decorator->isValid());
        $this->assertCount(1, $decorator->getErrors());
        $this->assertArrayHasKey('name', $decorator->getErrors());
        $request = (new ServerRequest('get', 'test'))->withParsedBody(['name' => '']);
        $validator = new Validator(App::$containerForFacade->get(\Doctrine\ORM\EntityManager::class), $request);
        $decorator = new ValidatorDecorator($validator);
        $decorator->required('name');
        $this->assertTrue($decorator->isValid());
        $this->assertCount(0, $decorator->getErrors());
        $this->assertArrayNotHasKey('name', $decorator->getErrors());
    }

    public function testPatternAndDecoratorFunctions()
    {
        $request = (new ServerRequest('get', 'test'))->withParsedBody(['name' => '16-04-1996']);
        $validator = new Validator(App::$containerForFacade->get(\Doctrine\ORM\EntityManager::class), $request);
        $decorator = new ValidatorDecorator($validator);
        $decorator->required('name')
            ->notEmpty('name')
            ->date('name', ValidatorDecorator::FR_DATE);
        $this->assertTrue($decorator->isValid());
        $this->assertCount(0, $decorator->getErrors());
        $this->assertArrayNotHasKey('name', $decorator->getErrors());

        $request = (new ServerRequest('get', 'test'))->withParsedBody(['name' => '16/04/1996']);
        $validator = new Validator(App::$containerForFacade->get(\Doctrine\ORM\EntityManager::class), $request);
        $decorator = new ValidatorDecorator($validator);
        $decorator->required('name')
            ->notEmpty('name')
            ->date('name', ValidatorDecorator::FR_DATE, '/');
        $this->assertTrue($decorator->isValid());
        $this->assertCount(0, $decorator->getErrors());
        $this->assertArrayNotHasKey('name', $decorator->getErrors());

        $request = (new ServerRequest('get', 'test'))->withParsedBody(['name' => '0681452163']);
        $validator = new Validator(App::$containerForFacade->get(\Doctrine\ORM\EntityManager::class), $request);
        $decorator = new ValidatorDecorator($validator);
        $decorator->required('name')
            ->notEmpty('name')
            ->mobilePhoneNumber('name');
        $this->assertTrue($decorator->isValid());
        $this->assertCount(0, $decorator->getErrors());
        $this->assertArrayNotHasKey('name', $decorator->getErrors());

        $request = (new ServerRequest('get', 'test'))->withParsedBody(['name' => '0981452163']);
        $validator = new Validator(App::$containerForFacade->get(\Doctrine\ORM\EntityManager::class), $request);
        $decorator = new ValidatorDecorator($validator);
        $decorator->required('name')
            ->notEmpty('name')
            ->mobilePhoneNumber('name');
        $this->assertFalse($decorator->isValid());
        $this->assertCount(1, $decorator->getErrors());
        $this->assertArrayHasKey('name', $decorator->getErrors());
    }


    public function testRulesValidatorContainer()
    {
        $request = (new ServerRequest('get', 'test'))->withParsedBody(['' => '']);
        $validator = new Validator(App::$containerForFacade->get(\Doctrine\ORM\EntityManager::class), $request);
        $decorator = new ValidatorDecorator($validator);
        $userValidator = new \Tests\Framework\Objects\UsersValidator($decorator);
        $this->assertCount(1, $userValidator->getErrors());
    }


}