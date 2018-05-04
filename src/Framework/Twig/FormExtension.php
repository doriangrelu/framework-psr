<?php

namespace Framework\Twig;

use Cake\Utility\Inflector;
use function DI\string;
use Framework\Validator\FieldTraduction;

class FormExtension extends \Twig_Extension
{
    private $optionsHtml = [
        //"required",
        "data-type" => "text",
        "data-required" => false,
        "disabled",
        "data-min" => false,
        "data-max" => false,
        "data-pattern" => false
    ];

    private $errorsHtml = [
        "mail" => "Veuillez saisir une adresse mail",
        "cp" => "Veuillez saisir un code postal",
        "phone" => "Veuillez saisir numéro de téléphone",
    ];

    private function getHelperWithFieldName(array $options, string $fieldName): string
    {
        if (isset($options["error"])) {
            return $options["error"];
        } else {
            if (isset($this->errorsHtml[$fieldName])) {
                return $this->errorsHtml[$fieldName];
            }
        }
        return "Veuillez compléter ce champs";
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('field', [$this, 'field'], [
                'is_safe' => ['html'],
                'needs_context' => true
            ]),
            new \Twig_SimpleFunction('name', [$this, "convertNameField"])
        ];
    }

    /**
     * Convertit le nom du champ depuis l'anglais, en français
     * @param string $fieldName
     * @return string
     */
    public function convertNameField(string $fieldName): string
    {
        $traductor = new FieldTraduction();
        return $traductor->getFrenchName($fieldName);
    }

    private function getValues($context, string $key)
    {
        $key = lcfirst(Inflector::underscore($key));
        if (isset($context["values"])) {
            $values = $context["values"];
            if (is_object($values)) {
                $values = (array)$values;
            }

            if (isset($values[$key])) {

                return $this->convertValue($values[$key]);
            } else {
                $key = lcfirst(Inflector::camelize($key));
                if (isset($values[$key])) {
                    return $this->convertValue($values[$key]);
                }
            }
            return null;
        }
        return null;
    }

    /**
     * @param array $context
     * @param string $key
     * @param array $options
     * @return string
     */
    public function field(array $context, string $key, array $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $error = $this->getErrorHtml($context, $key);
        $class = 'form-group ';
        $class .= $options["divClass"] ?? "";
        $fieldId = $key;
        if (isset($options["label"])) {
            $label = ucfirst($options["label"]);
        } else {
            $label = ucfirst($this->convertNameField($key));
        }
        if (isset($options["empty"]) && $options["empty"]) {
            $value = null;
        } else {
            if (isset($options["value"])) {
                $value = $this->convertValue($options["value"]);
            } else {
                $value = $this->convertValue($this->getValues($context, $key));
            }
        }
        $otherAttributes = [];

        $help = isset($options["help"]) ? '<small id="passwordHelpBlock" class="form-text text-muted">' . $options["help"] . '</small>' : null;
        $attributes = [
            'class' => trim('form-control ' . ($options['class'] ?? '')),
            'name' => $key,
            'id' => $key,

        ];
        if (isset($options["rule"])) {
            $options["data"] = ["rule" => $options["rule"]];
        }

        if (isset($options["data"])) {
            $attributes["data"] = $options["data"];

        }
        foreach ($options as $key => $attr) {
            $name = "data-$key";
            if (array_key_exists($name, $this->optionsHtml)) {
                if ($key == "required") {
                    $attr = "true";
                    if (isset($attributes["class"])) {
                        $attributes["class"] .= " required";
                    } else {
                        $attributes["class"] = " required";
                    }
                }
                $otherAttributes[$name] = $attr;
            }
        }

        $attributes = array_merge($attributes, $otherAttributes);

        if ($error) {
            $class .= ' has-danger';
            $attributes['class'] .= ' form-control-danger';
        }
        $attributes["placeholder"] = $label;
        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } elseif (array_key_exists('options', $options)) {
            $input = $this->select($value, $options['options'], $attributes);
        } else {
            if ($type != "text" && $type != "number" && $type != "password") {
                $type = "text";
            }
            $input = $this->input($value, $attributes, $type);
        }
        return "<div class=\"" . $class . "\">
              <label for=\"name\">{$label}</label>
              {$input}
              {$error}
              <div class='error-data-entry'>
                 {$this->getHelperWithFieldName($options, $fieldId)}
              </div>
              </div>
              ";
    }


    private function convertValue($value): string
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return (string)$value;
    }

    /**
     * Génère l'HTML en fonction des erreurs du contexte
     * @param $context
     * @param $key
     * @return string
     */
    private function getErrorHtml($context, $key)
    {
        $error = $context['errors'][$key] ?? false;
        if ($error) {
            return " <small class=\"form-text text-muted\">{$error}</small>";
        }
        return "";
    }

    /**
     * @param null|string $value
     * @param array $attributes
     * @param string $type
     * @return string
     */
    private
    function input(?string $value, array $attributes, string $type): string
    {
        return "<input type=\"$type\" " . $this->getHtmlFromArray($attributes) . " value=\"{$value}\" {$this->data($attributes)}>";
    }

    private
    function data(array $attributes):?string
    {
        if (isset($attributes["data"])) {
            $data = "";
            foreach ($attributes["data"] as $name => $value) {
                $data .= "data-$name=\"$value\" ";
            }
            return trim($data);
        }
        return null;
    }

    /**
     * Génère un <textarea>
     * @param null|string $value
     * @param array $attributes
     * @return string
     */
    private
    function textarea(?string $value, array $attributes): string
    {
        return "<textarea " . $this->getHtmlFromArray($attributes) . ">{$value}</textarea>";
    }

    /**
     * Génère un <select>
     * @param null|string $value
     * @param array $options
     * @param array $attributes
     * @return string
     */
    private
    function select(?string $value, array $options, array $attributes)
    {
        $htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . '<option ' . $this->getHtmlFromArray($params) . '>' . $options[$key] . '</option>';
        }, "");

        return "<select " . $this->getHtmlFromArray($attributes) . ">$htmlOptions</select>";
    }

    /**
     * Transforme un tableau $clef => $valeur en attribut HTML
     * @param array $attributes
     * @return string
     */
    private
    function getHtmlFromArray(array $attributes)
    {
        $htmlParts = [];
        foreach ($attributes as $key => $value) {
            if (!is_array($value)) {
                if ($value === true) {
                    $htmlParts[] = (string)$key;
                } elseif ($value !== false) {
                    $htmlParts[] = "$key=\"$value\"";
                }
            }

        }
        return implode(' ', $htmlParts);
    }
}
