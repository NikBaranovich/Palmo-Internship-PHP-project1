<?php
namespace Palmo\Core\service\Validation;

use Palmo\Core\service\Validation\CommonValidationTrait;
use Palmo\Core\service\Validation\ValidationRules;

class PasswordValidation implements ValidationRules
{
    use CommonValidationTrait;

    public static function validate($data)
    {
        $result = match (false) {
            self::validateEmpty($data) => "Password is required",
            self::validateMinLength($data, 6) => "Password must contain at least 6 characters",
            self::validateMaxLength($data, 30) => "Password must contain no more than 30 characters",
            default => null
        };

        return $result;
    }
}