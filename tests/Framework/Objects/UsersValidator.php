<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 02/12/2018
 * Time: 15:24
 */

namespace Tests\Framework\Objects;

use Framework\Decorator\ValidatorDecorator;
use Framework\Validator\AbstractRules;

class UsersValidator extends AbstractRules
{

    protected function _build(): void
    {
        $validator = $this->_getValidatorInstance();
        $validator->required('name');
    }

}