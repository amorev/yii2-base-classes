<?php

namespace Zvinger\BaseClasses\app\modules\api\base\exceptions;


class RecaptchaSecretNotFound extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = NULL)
    {
        $message = 'Recaptcha secret key not found';
        parent::__construct(400, $message, $code, $previous);
    }
}
