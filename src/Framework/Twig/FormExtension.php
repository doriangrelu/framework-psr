<?php

namespace Framework\Twig;

use Framework\Middleware\CsrfMiddleware;
use Framework\Middleware\HttpMethodMiddleware;


class FormExtension extends \Twig_Extension
{


    /**
     * @var CsrfMiddleware
     */
    private $_csrfMiddleware;
    /**
     * @var HttpMethodMiddleware
     */
    private $_httpMethodMiddleware;

    /**
     * @var null
     */
    private $_entity = null;

    /**
     * FormHelper constructor.
     * @param CsrfMiddleware $csrfMiddleware
     * @param HttpMethodMiddleware $httpMethodMiddleware
     */
    public function __construct(CsrfMiddleware $csrfMiddleware, HttpMethodMiddleware $httpMethodMiddleware)
    {
        $this->_csrfMiddleware = $csrfMiddleware;
        $this->_httpMethodMiddleware = $httpMethodMiddleware;
    }

    /**
     * @param $entity
     * @param $url
     * @param string $method
     * @param array $options
     * @return string
     * @throws \App\Framework\Exception\Http\HttpException
     */
    public function create($entity, $url, $method = 'POST', array $options = ['class' => 'form-test']): string
    {
        $this->_entity = $entity;
        return <<<EOD
<form method="$method" action="$url" {$this->_getOptions($options)}> 
{$this->getCsrfHiddenInput()}
{$this->getFormMethodHiddenInput($method)}
EOD;
    }

    /**
     * @param string $method
     * @return string
     * @throws \App\Framework\Exception\Http\HttpException
     */
    private function getFormMethodHiddenInput(string $method): string
    {
        $this->_httpMethodMiddleware->allowedMethodOrFail($method);
        return '<input type="hidden" ' .
            'name="' . $this->_httpMethodMiddleware->getFieldName() . '" ' .
            'value="' . $method . '"/>';
    }

    /**
     * @return string
     */
    public function getCsrfHiddenInput(): string
    {
        return '<input type="hidden" ' .
            'name="' . $this->_csrfMiddleware->getFormKey() . '" ' .
            'value="' . $this->_csrfMiddleware->generateToken() . '"/>';
    }

    /**
     * @param string $name
     * @param string $type
     * @param array $options
     * @return string
     */
    public function input(string $name, string $type = 'text', array $options = ['class' => 'form-control']): string
    {
        return <<<EDO
<input type="$type" name="$name" value="" {$this->_getOptions($options)}>
EDO;
    }

    /**
     * @param string $name
     * @param bool $required
     * @param string $requiredClass
     * @return string
     */
    public function getLabel(string $name, $required = false, $requiredClass = "required"): string
    {
        $options = [];
        if ($required) {
            $options = ['class' => $requiredClass];
        }
        return <<<EOD
<label {$this->_getOptions($options)}>$name</label>
EOD;

    }

    /**
     * @param string $name
     * @param array $valuesList
     * @param array $options
     * @return string
     */
    public function select(string $name, array $valuesList, array $options = [])
    {
        $selectOptions = [];
        foreach ($valuesList as $value => $text) {
            $selectOptions[] = <<<EOD
<option value="$value">$text</option>
EOD;
        }
        $selectOptionsString = implode(PHP_EOL, $selectOptions);
        return <<<EOD
<select name="$name" {$this->_getOptions($options)}>
$selectOptionsString
</select>
EOD;
    }

    /**
     * @return string
     */
    public function end()
    {
        return <<<EOD
</form>
EOD;
    }

    /**
     * @param array $options
     * @return string
     */
    private function _getOptions(array $options): string
    {
        $optionClean = [];
        foreach ($options as $name => $values) {
            $optionClean[] = <<<EOD
$name="$values"
EOD;
        }
        return implode(" ", $optionClean);
    }


    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('FormCreate', [$this, 'create'], [
                'is_safe' => ['html'],
                'needs_context' => false
            ]),
            new \Twig_SimpleFunction('FormEnd', [$this, 'end'], [
                'is_safe' => ['html'],
                'needs_context' => false
            ]),
            new \Twig_SimpleFunction('FormInput', [$this, 'input'], [
                'is_safe' => ['html'],
                'needs_context' => false
            ]),
            new \Twig_SimpleFunction('FormSelect', [$this, 'select'], [
                'is_safe' => ['html'],
                'needs_context' => false
            ]),
        ];
    }


}
