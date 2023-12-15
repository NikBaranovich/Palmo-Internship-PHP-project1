<?php
namespace Palmo\Core\service\Validation;

use Palmo\Core\service\Validation\CommonValidationTrait;
use Palmo\Core\service\Validation\ValidationRules;

class UsernameValidation implements ValidationRules
{
    use CommonValidationTrait;

    public static function validate($data)
    {
        $result = match (false) {
            self::validateEmpty($data) => "Username is required",
            self::validateMinLength($data, 3) => "Username must contain at least 3 characters",
            self::validateMaxLength($data, 30) => "Username must contain no more than 30 characters",
            default => null
        };

        return $result;
    }
}