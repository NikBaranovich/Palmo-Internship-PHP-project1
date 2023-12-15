<?php

namespace Palmo\Core\service;

use Palmo\Core\service\Validation\EmailValidation;
use Palmo\Core\service\Validation\PasswordValidation;
use Palmo\Core\service\Validation\UsernameValidation;
use Palmo\Core\service\Validation\DateValidation;
use Palmo\Core\service\Validation\TitleValidation;
use Palmo\Core\service\Validation\EndDateValidation;
use Palmo\Core\service\Validation\ImageValidation;

class Validation
{
    static function validate($type, ...$data)
    {
        $error = match ($type) {
            'email' => EmailValidation::validate($data[0]),
            'password' => PasswordValidation::validate($data[0]),
            'username' => UsernameValidation::validate($data[0]),
            'date' => DateValidation::validate($data[0]),
            'title' => TitleValidation::validate($data[0]),
            'endDate' => EndDateValidation::validate($data[0], $data[1]),
            'image' => ImageValidation::validate($data[0]),
            default => null,
        };
        return $error;
    }
}
