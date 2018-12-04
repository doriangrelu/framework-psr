<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 01/12/2018
 * Time: 14:48
 */

namespace Framework\Decorator;


use Framework\Validator\ValidatorInterface;

class ValidatorDecorator extends Decorator implements ValidatorInterface
{
    const EN_DATE = 1;
    const FR_DATE = 0;

    /**
     * @var ValidatorInterface
     */
    private $_validator;

    /**
     * ValidatorDecorator constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->_validator = $validator;
    }

    /**
     * @param string $fieldName
     * @return ValidatorDecorator
     */
    public function require(string $fieldName): ValidatorInterface
    {
        $this->_validator->required($fieldName);
        return $this;
    }

    /**
     * @param array $sizes
     * @param bool $secureBody
     * @return ValidatorDecorator
     */
    public function truncateBody(array $sizes, $secureBody = true): ValidatorInterface
    {
        $this->_validator->truncateBody($sizes, $secureBody);
        return $this;
    }

    /**
     * @param array $allowedFields
     * @param bool $flip
     * @return ValidatorDecorator
     */
    public function allowFields(array $allowedFields, $flip = true): ValidatorInterface
    {
        $this->_validator->allowFields($allowedFields, $flip);
        return $this;
    }

    /**
     * @param string $fieldName
     * @param string $tableName
     * @return ValidatorDecorator
     */
    public function unique(string $fieldName, string $tableName): ValidatorInterface
    {
        $this->_validator->unique($fieldName, $tableName);
        return $this;
    }

    /**
     * @param string $fieldName
     * @param string $tableName
     * @param string $tableFieldName
     * @return ValidatorDecorator
     */
    public function existsIn(string $fieldName, string $tableName, string $tableFieldName = 'id'): ValidatorInterface
    {
        $this->_validator->existsIn($fieldName, $tableName, $tableFieldName);
        return $this;
    }

    /**
     * @param string $fieldName
     * @param string $pattern
     * @param string $formatText
     * @param bool $isFormated
     * @return ValidatorDecorator
     */
    public function pattern(string $fieldName, string $pattern, string $formatText, bool $isFormated = false): ValidatorInterface
    {
        $this->_validator->pattern($fieldName, $pattern, $formatText, $isFormated);
        return $this;
    }

    /**
     * @param string $fieldName
     * @return ValidatorDecorator
     */
    public function allowEmpty(string $fieldName): ValidatorInterface
    {
        $this->_validator->allowEmpty($fieldName);
        return $this;
    }

    /**
     * @param string $fieldName
     * @return ValidatorDecorator
     */
    public function notEmpty(string $fieldName): ValidatorInterface
    {
        $this->_validator->notEmpty($fieldName);
        return $this;
    }

    /**
     * @param string $fieldName
     * @return ValidatorDecorator
     */
    public function required(string $fieldName): ValidatorInterface
    {
        $this->_validator->required($fieldName);
        return $this;
    }

    /**
     * @param string $fieldName
     * @param int $sizeMin
     * @param int $sizeMax
     * @return ValidatorDecorator
     */
    public function limit(string $fieldName, int $sizeMin, int $sizeMax = 255): ValidatorInterface
    {
        $this->_validator->limit($fieldName, $sizeMin, $sizeMax);
        return $this;
    }

    public function isValid(): bool
    {
        return $this->_validator->isValid();
    }

    public function getErrors(): array
    {
        return $this->_validator->getErrors();
    }

    public function getHTMLError(string $fieldName): string
    {
        return $this->_validator->getHTMLError($fieldName);
    }

    public function getError(string $fieldName): ?array
    {
        return $this->_validator->getError($fieldName);
    }

    /**
     * Decorated method - Check if is mobile phone number
     * @param $fieldName
     * @return ValidatorDecorator
     */
    public function mobilePhoneNumber($fieldName): ValidatorInterface
    {
        $this->_validator->pattern($fieldName, '0[6-7][0-9]{8}', 'Numéro de téléphone mobile');
        return $this;
    }

    /**
     * Decorated method - Check if is email address
     * @param $fieldName
     * @return $this
     */
    public function email(string $fieldName): ValidatorInterface
    {
        $this->_validator->pattern($fieldName, '[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*', 'Numéro de téléphone mobile');
        return $this;
    }

    /**
     * Decorated method - Check date format
     * @param string $fieldName
     * @param string $lang
     * @param string $separator
     * @return ValidatorDecorator
     */
    public function date(string $fieldName, string $lang = self::FR_DATE, $separator = '-'): ValidatorInterface
    {
        $separator = $separator ==='/' ? '\/' : $separator;
        switch ($lang) {
            case self::EN_DATE:
                $formatDate = 'yyyy' . $separator . 'mm' . $separator . 'jj';
                $pattern = '[0-9]{4}' . $separator . '[0-9]{2}' . $separator . '[0-9]{2}';
                break;
            default:
                $formatDate = 'jj' . $separator . 'mm' . $separator . 'yyyy';
                $pattern = '[0-9]{2}' . $separator . '[0-9]{2}' . $separator . '[0-9]{4}';
                break;
        }
        $this->_validator->pattern($fieldName, $pattern, "Date au format $formatDate");
        return $this;
    }

    /**
     * @param string $template
     * @return ValidatorInterface
     */
    public function setErrorsTemplate(string $template): ValidatorInterface
    {
        $this->_validator->setErrorsTemplate($template);
        return $this;
    }
}