<?php
namespace Palmo\Core\service\Validation;

use Palmo\Core\service\Validation\CommonValidationTrait;
use Palmo\Core\service\Validation\DateValidation;

class EndDateValidation extends DateValidation
{
    use CommonValidationTrait;

    public static function validate(...$dates)
    {
        $result = match (false) {

            self::validateEndDate($dates[0], $dates[1]) => "End date must be later than start date",
            default => null
        };

        return $result;
    }

    static function validateEndDate($startDate, $endDate)
    {
        $dateTimestamp1 = strtotime($startDate);
        $dateTimestamp2 = strtotime($endDate);
        return $dateTimestamp1 <= $dateTimestamp2;
    }
}
