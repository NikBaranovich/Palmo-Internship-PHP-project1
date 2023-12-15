<?php
namespace Palmo\Core\service\Validation;

trait FileValidationTrait
{
    public static function validateEmpty($data)
    {
        return $data['size'] > 0;
    }
    public static function validateMax($data, $maxSize)
    {
        return $data['size'] < $maxSize;
    }
    public static function validateFileType($data, $fileType)
    {
        return $data['type'] == $fileType;
    }
}