<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 02/12/2018
 * Time: 13:46
 */

namespace Framework\Validator;

use Framework\Interfaces\Serializable;
use Framework\Utility\ClassUtility;
use Framework\Validator\ValidatorInterface;


/**
 * Class abstractRules
 * @package Framework\Validator
 */
abstract class AbstractRules implements Serializable
{

    use ClassUtility;

    /**
     * @var ValidatorInterface | null
     */
    private $_validator;

    /**
     * abstractRules constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->_validator = $validator;
        $this->_build();
    }


    /**
     * @return ValidatorInterface
     */
    protected function _getValidatorInstance(): ValidatorInterface
    {
        return $this->_validator;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->_getValidatorInstance()->getErrors();
    }

    /**
     * @param string $fieldName
     * @return string
     */
    public function getHTMLError(string $fieldName): string
    {
        return $this->_getValidatorInstance()->getHTMLError($fieldName);
    }

    /**
     * @param string $fieldName
     * @return array|null
     */
    public function getError(string $fieldName): ?array
    {
        return $this->_getValidatorInstance()->getError($fieldName);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->_getValidatorInstance()->isValid();
    }

    /**
     * Define many validation rule wich want to use in differents models or controllers.
     */
    abstract protected function _build(): void;

    /**
     * Return Serailized errors
     * @return string
     */
    public function __toString(): string
    {
        return $this->serialize();
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        return json_encode($this->_getValidatorInstance()->getErrors());
    }

}