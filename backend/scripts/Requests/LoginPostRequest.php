<?php
namespace Palmo\Core\Requests;

use Palmo\Core\service\Validation;
use Palmo\Core\service\PostRequest;

class LoginPostRequest extends Validation implements PostRequest
{
    public static function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:8|max:30',
        ];
    }
    public function __construct()
    {
        parent::__construct(LoginPostRequest::rules());
    }
}
