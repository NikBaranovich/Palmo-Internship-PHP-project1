<?php

namespace Palmo\Core\service\Validation;

trait CommonValidationTrait
{
    public static function validateEmpty($data)
    {
        return !empty($data);
    }

    public static function validateMinLength($data, $minLength)
    {
        return mb_strlen($data) >= $minLength;
    }

    public static function validateMaxLength($data, $maxLength)
    {
        return mb_strlen($data) <= $maxLength;
    }
}