<?php

namespace Palmo\Core\service;

interface PostRequest{
    public static function rules();
}
class Validation
{
    protected $rules;

    protected $errors = [];

    public function __construct($rules)
    {
        $this->rules = $rules;
    }

    public function validate($data)
    {
        foreach ($this->rules as $field => $rule) {
            $rulesArray = explode('|', $rule);
            foreach ($rulesArray as $singleRule) {
                $this->applyRule($field, $singleRule, $data);
            }
        }
        return $this->errors;
    }

    protected function applyRule($field, $rule, $data)
    {
        $parameters = [];
        if (strpos($rule, ':') !== false) {
            list($rule, $parameter) = explode(':', $rule);
            $parameters = explode(',', $parameter);
        }

        $methodName = 'validate' . ucfirst($rule);
        if (method_exists($this, $methodName)) {
            $this->$methodName($field, $data, $parameters);
        }
    }

    protected function validateRequired($field, $data)
    {
        if (!isset($data[$field]) || empty($data[$field])) {
            $this->addError($field, 'Field is required.');
        }
    }

    protected function validateMin($field, $data, $parameters)
    {
        $minLength = $parameters[0];
        if (strlen($data[$field]) < $minLength) {
            $this->addError($field, "Field must be at least $minLength characters long.");
        }
    }

    protected function validateMax($field, $data, $parameters)
    {
        $maxLength = $parameters[0];
        if (strlen($data[$field]) > $maxLength) {
            $this->addError($field, "Field must not exceed $maxLength characters.");
        }
    }

    protected function addError($field, $message)
    {
        $this->errors[$field][] = $message;
    }
}
