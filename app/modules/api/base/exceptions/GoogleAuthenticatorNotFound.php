<?php
/**
 * Created by PhpStorm.
 * User: nik
 * Date: 20.05.19
 * Time: 14:37
 */

namespace app\modules\api\base\exceptions;


class GoogleAuthenticatorNotFound extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = NULL)
    {
        $message = 'Google Authenticator not found.';
        parent::__construct(400, $message, $code, $previous);
    }
}
