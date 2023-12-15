<?php
namespace Palmo\Core\service\Validation;

use Palmo\Core\service\Validation\CommonValidationTrait;
use Palmo\Core\service\Validation\ValidationRules;

class TitleValidation implements ValidationRules
{
    use CommonValidationTrait;

    public static function validate($data)
    {
        $result = match (false) {
            self::validateEmpty($data) => "Title is required",
            default => null
        };

        return $result;
    }
}
