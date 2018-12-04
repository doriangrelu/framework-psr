<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 27/11/2018
 * Time: 09:30
 */

namespace Framework\Validator;

use App\Framework\Exception\Type\UnexpectedTypeException;
use App\Framework\Exception\ValidatorException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Validator
 * @package Framework\Validator
 */
class Validator implements ValidatorInterface
{
    const MAX_SIZE = 1;
    const MIN_SIZE = 2;
    const PATTERN = 3;
    const REQUIRED = 4;
    const UNIQUE = 5;
    const EXISTS = 6;
    const NOT_EMPTY = 7;

    /**
     * @var array
     */
    private $_textErrors = [
        self::MAX_SIZE => 'La taille du champs doit être inférieure à {{size}}',
        self::MIN_SIZE => 'La taille du champs doit être supérieur à {{size}}',
        self::PATTERN => 'Le champs doit respecter le format suivant: {{format}}',
        self::REQUIRED => 'Le champs est recquis',
        self::UNIQUE => 'La valeur est déjà utilisée',
        self::EXISTS => "La valeur n'existe pas",
        self::NOT_EMPTY => "Le champs ne peut être vide"
    ];

    private $_errorFieldTemplate = <<<EOD
<div class="alert alert-danger form-error">
{{errors}}
</div>
EOD;


    /**
     * @var string[]
     */
    private $_allowedEmpty = [];

    /**
     * @var string[]
     */
    private $_emptyFields = [];

    /**
     * @var EntityManagerInterface
     */
    private $_doctrineService;

    /**
     * @var array
     */
    private $_errors = [];

    /**
     * @var array|null|object
     */
    private $_data = [];


    /**
     * Validator constructor.
     * @param EntityManagerInterface $entityManager
     * @param ServerRequestInterface $request
     */
    public function __construct(EntityManagerInterface $entityManager, ServerRequestInterface $request)
    {
        $this->_doctrineService = $entityManager;
        $this->_data = $request->getParsedBody();
    }

    /**
     * @param string $template
     * @return Validator
     * @throws ValidatorException
     */
    public function setErrorsTemplate(string $template): ValidatorInterface
    {
        if (strpos($template, '{{errors}}') === false) {
            throw new ValidatorException("Missing flag {{errors}} in error template definition.");
        }
        $this->_errorFieldTemplate = $template;
        return $this;
    }

    /**
     * @param array $sizes
     * @param bool $secureBody
     * @return Validator
     * @throws UnexpectedTypeException
     */
    public function truncateBody(array $sizes, $secureBody = true): ValidatorInterface
    {
        if ($secureBody) {
            $this->allowFields($sizes, false);
        }
        foreach ($sizes as $fieldName => $size) {
            $value = $this->_getValue($fieldName);
            if (!is_null($value)) {
                if (!is_int($size)) {
                    throw new UnexpectedTypeException("Integer value expected, not: {$size}");
                }
                $this->_data[$fieldName] = substr($value, 0, $size);
            }
        }
        return $this;
    }

    /**
     * @param array $allowedFields
     * @param bool $flip
     * @return Validator
     */
    public function allowFields(array $allowedFields, $flip = true): ValidatorInterface
    {
        if ($flip) {
            $allowedFields = array_flip($allowedFields);
        }
        foreach ($this->_data as $fieldName => $value) {
            if (!isset($allowedFields[$fieldName])) {
                unset($this->_data[$fieldName]);
            }
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param string $tableName
     * @return Validator
     */
    public function unique(string $fieldName, string $tableName): ValidatorInterface
    {
        $repository = $this->_doctrineService->getRepository($tableName);

        $value = $this->_getValue($fieldName);
        if (!is_null($value)) {
            $entity = $repository->findBy([
                $fieldName => $value,
            ]);
            if (!is_null($entity)) {
                $this->_addError($fieldName, self::UNIQUE);
            }
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param string $tableName
     * @param string $tableFieldName
     * @return Validator
     */
    public function existsIn(string $fieldName, string $tableName, string $tableFieldName = 'id'): ValidatorInterface
    {
        $value = $this->_getValue($fieldName);
        if (is_null($value)) {
            $this->_addError($fieldName, self::EXISTS);
            return $this;
        }
        $repository = $this->_doctrineService->getRepository($tableName);
        $entity = $repository->findBy([
            $tableFieldName => $value,
        ]);
        if (is_null($entity)) {
            $this->_addError($fieldName, self::EXISTS);
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param string $pattern
     * @param string $formatText
     * @param bool $isFormated
     * @return Validator
     */
    public function pattern(string $fieldName, string $pattern, string $formatText, bool $isFormated = false): ValidatorInterface
    {
        $value = $this->_getValue($fieldName);
        if (is_null($value)) {
            $this->_addError($fieldName, self::PATTERN, ['format' => $formatText]);
            return $this;
        }
        $pattern = $this->_makePattern($pattern, $isFormated);
        if (preg_match($pattern, $value) == false) {
            $this->_addError($fieldName, self::PATTERN, ['format' => $formatText]);
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @return Validator
     */
    public function allowEmpty(string $fieldName): ValidatorInterface
    {
        $value = $this->_getValue($fieldName);
        if (is_null($value) || (trim($value)) == '') {
            $this->_allowedEmpty[] = $fieldName;
            $this->_cleanAllErrors($fieldName);
        }
        return $this;
    }

    public function notEmpty(string $fieldName): ValidatorInterface
    {
        $value = $this->_getValue($fieldName);
        if (is_null($value) || (trim($value)) == '') {
            $this->_emptyFields[] = $fieldName;
            $this->_cleanAllErrors($fieldName);
            $this->_addError($fieldName, self::NOT_EMPTY);
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @return Validator
     */
    public function required(string $fieldName): ValidatorInterface
    {
        if (is_null($this->_getValue($fieldName))) {
            $this->_cleanAllErrors($fieldName);
            $this->_emptyFields[] = $fieldName;
            $this->_addError($fieldName, self::REQUIRED);
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param int $sizeMin
     * @param int $sizeMax
     * @return Validator
     */
    public function limit(string $fieldName, int $sizeMin, int $sizeMax = 255): ValidatorInterface
    {
        if (!is_null($this->_getValue($fieldName))) {
            $size = mb_strlen($this->_getValue($fieldName));
            if ($sizeMax > 0 && $size > $sizeMax) {
                $this->_addError($fieldName, self::MAX_SIZE, ['size' => $sizeMax]);
            }

            if ($sizeMin > $size) {
                $this->_addError($fieldName, self::MIN_SIZE, ['size' => $sizeMin]);
            }
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param string $errorKey
     * @param array $replacements
     */
    private function _addError(string $fieldName, string $errorKey, array $replacements = []): void
    {
        $flippedEmpty = array_flip($this->_emptyFields);
        if (isset($flippedEmpty[$fieldName]) && (int)$errorKey !== (int)self::NOT_EMPTY && (int)$errorKey !== (int)self::REQUIRED) {
            return;
        }

        $flippedArray = array_flip($this->_allowedEmpty);
        if (!isset($flippedArray[$fieldName])) {
            $text = $this->_textErrors[$errorKey] ?? 'Le champs est recquis au bon format';
            $this->_errors[$fieldName][] = $this->_replaceWith($text, $replacements);
        } else {
            $this->_cleanAllErrors($fieldName);
        }
    }

    /**
     * @param string $fieldName
     */
    private function _cleanAllErrors(string $fieldName): void
    {
        if (isset($this->_errors[$fieldName])) {
            unset($this->_errors[$fieldName]);
        }
    }

    /**
     * @param string $pattern
     * @param bool $isFormated
     * @return string
     */
    private function _makePattern(string $pattern, bool $isFormated): string
    {
        if ($isFormated) {
            return $pattern;
        }

        return '/^' . $pattern . '$/';
    }

    /**
     * @param string $message
     * @param array $replacements
     * @return string
     */
    private function _replaceWith(string $message, array $replacements): string
    {
        foreach ($replacements as $alias => $value) {
            $message = str_replace($this->_makeAlias($alias), $value, $message);
        }
        return $message;
    }

    /**
     * @param string $alias
     * @return string
     */
    private function _makeAlias(string $alias): string
    {
        return '{{' . $alias . '}}';
    }

    /**
     * @param string $fieldName
     * @return null|string
     */
    private function _getValue(string $fieldName): ?string
    {
        return $this->_data[$fieldName] ?? null;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return empty($this->_errors);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->_errors;
    }

    /**
     * @param string $fieldName
     * @return string
     */
    public function getHTMLError(string $fieldName): string
    {
        $errors = $this->_errors[$fieldName] ?? [];
        if (empty($errors)) {
            return '';
        }
        $listHTML = <<<EOD
<ul>
{{li}}
</ul>
EOD;
        $errors = array_map(function ($value) {
            return "<li>$value</li>";
        }, $errors);
        $listHTML = str_replace('{{li}}', implode(PHP_EOL, $errors), $listHTML);
        return str_replace('{{errors}}', $listHTML, $this->_errorFieldTemplate);
    }

    /**
     * @param string $fieldName
     * @return array|null
     */
    public function getError(string $fieldName): ?array
    {
        return $this->_errors[$fieldName] ?? null;
    }

}