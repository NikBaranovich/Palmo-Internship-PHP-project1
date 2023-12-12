<?php

namespace Palmo\Core\service;

interface ValidationRules
{
    public static function validate($data);
}
class Validation
{
    static function validate($type, ...$data)
    {
        switch ($type) { //todo match
            case 'email':
                $error = ValidateEmail::validate($data[0]);
                break;
            case 'password':
                $error = ValidatePassword::validate($data[0]);
                break;
            case 'username':
                $error = ValidateUsername::validate($data[0]);
                break;
            case 'date':
                $error = ValidateDate::validate($data[0]);
                break;
            case 'title':
                $error = ValidateTitle::validate($data[0]);
                break;
            case 'endDate':
                $error = ValidateEndDate::validate($data[0], $data[1]);
                break;
            case 'image':
                $error = ValidateImage::validate($data[0]);
                break;
            default:
                $error = null;
                break;
        }
        return $error;
    }
}

trait CommonValidation
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

trait FileValidation
{
    public static function validateEmpty($data)
    {
        return $data['size'] > 0;
    }
    public static function validateMax($data, $maxSize){
        return $data['size'] < $maxSize;
    }
    public static function validateFileType($data, $fileType){
        return $data['type'] == $fileType;
    }
}

class ValidateEmail implements ValidationRules
{

    use CommonValidation;


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

class ValidatePassword implements ValidationRules
{
    use CommonValidation;

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

class ValidateUsername implements ValidationRules
{
    use CommonValidation;

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

class ValidateDate implements ValidationRules
{
    use CommonValidation;

    public static function validate($data)
    {
        $result = match (false) {
            self::validateEmpty($data) => "Date is required",
            default => null
        };

        return $result;
    }

    static function validateDateFormat($data)
    {
        return date_create_from_format('Y-m-d', $data);
    }
}

class ValidateEndDate extends ValidateDate
{
    use CommonValidation;

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

class ValidateTitle implements ValidationRules
{
    use CommonValidation;

    public static function validate($data)
    {
        $result = match (false) {
            self::validateEmpty($data) => "Title is required",
            default => null
        };

        return $result;
    }
}
class ValidateImage implements ValidationRules
{
    use CommonValidation, FileValidation{
        FileValidation::validateEmpty insteadof CommonValidation;
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
