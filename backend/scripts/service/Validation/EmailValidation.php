<?php
namespace Palmo\Core\service\Validation;

use Palmo\Core\service\Validation\CommonValidationTrait;
use Palmo\Core\service\Validation\ValidationRules;

class EmailValidation implements ValidationRules
{
    use CommonValidationTrait;

    public static function validate($data)
    {
        $result = match (false) {
            self::validateEmpty($data) => "Email is required",
            self::validateEmail($data) => "Invalid email format",
            default => null
        };

        return $result;
    }

    private static function validateEmail($data)
    {
        if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }
}