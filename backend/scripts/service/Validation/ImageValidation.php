<?php
namespace Palmo\Core\service\Validation;

use Palmo\Core\service\Validation\CommonValidationTrait;
use Palmo\Core\service\Validation\FileValidationTrait;
use Palmo\Core\service\Validation\ValidationRules;

class ImageValidation implements ValidationRules
{
    use CommonValidationTrait, FileValidationTrait {
        FileValidationTrait::validateEmpty insteadof CommonValidationTrait;
    }

    public static function validate($data)
    {
        $result = match (false) {
            self::validateEmpty($data) => "File is empty",
            self::validateMax($data, 1000000) => "File size is too big",
            self::validateFileType($data, "image/jpeg") => "Unsupported image format",
            default => null
        };

        return $result;
    }
}
