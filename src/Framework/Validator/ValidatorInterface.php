<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 02/12/2018
 * Time: 15:31
 */

namespace Framework\Validator;

interface ValidatorInterface
{
    /**
     * @param string $fieldName
     * @return ValidatorInterface
     */
    public function required(string $fieldName): ValidatorInterface;

    /**
     * @param string $fieldName
     * @param int $sizeMin
     * @param int $sizeMax
     * @return ValidatorInterface
     */
    public function limit(string $fieldName, int $sizeMin, int $sizeMax = 255): ValidatorInterface;

    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return array
     */
    public function getErrors(): array;

    /**
     * @param string $fieldName
     * @return string
     */
    public function getHTMLError(string $fieldName): string;

    /**
     * @param string $fieldName
     * @return array|null
     */
    public function getError(string $fieldName): ?array;

    /**
     * @param string $template
     * @return ValidatorInterface
     */
    public function setErrorsTemplate(string $template): ValidatorInterface;

    /**
     * @param array $sizes
     * @param bool $secureBody
     * @return ValidatorInterface
     */
    public function truncateBody(array $sizes, $secureBody = true): ValidatorInterface;

    /**
     * @param array $allowedFields
     * @param bool $flip
     * @return ValidatorInterface
     */
    public function allowFields(array $allowedFields, $flip = true): ValidatorInterface;

    /**
     * @param string $fieldName
     * @param string $tableName
     * @return ValidatorInterface
     */
    public function unique(string $fieldName, string $tableName): ValidatorInterface;

    /**
     * @param string $fieldName
     * @param string $tableName
     * @param string $tableFieldName
     * @return ValidatorInterface
     */
    public function existsIn(string $fieldName, string $tableName, string $tableFieldName = 'id'): ValidatorInterface;

    /**
     * @param string $fieldName
     * @param string $pattern
     * @param string $formatText
     * @param bool $isFormated
     * @return Validator
     */
    public function pattern(string $fieldName, string $pattern, string $formatText, bool $isFormated = false): ValidatorInterface;

    /**
     * @param string $fieldName
     * @return ValidatorInterface
     */
    public function allowEmpty(string $fieldName): ValidatorInterface;

    /**
     * @param string $fieldName
     * @return ValidatorInterface
     */
    public function notEmpty(string $fieldName): ValidatorInterface;

}