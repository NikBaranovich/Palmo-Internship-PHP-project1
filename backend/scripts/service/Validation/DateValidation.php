<?php
namespace Palmo\Core\service\Validation;

use Palmo\Core\service\Validation\CommonValidationTrait;
use Palmo\Core\service\Validation\ValidationRules;

class DateValidation implements ValidationRules
{
    use CommonValidationTrait;

    public static function validate($data)
    {
        $result = match (false) {
            self::validateEmpty($data) => "Date is required",
            self::validateDateFormat($data) => "Date is incorrect format",
            default => null
        };

        return $result;
    }

    static function validateDateFormat($data)
    {
        return date_create_from_format('Y-m-d', $data);
    }
}