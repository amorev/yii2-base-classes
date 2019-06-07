<?php

namespace Zvinger\BaseClasses\app\modules\api\base\exceptions;


class RecaptchaNotFound extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = NULL)
    {
        $message = 'Recaptcha not found.';
        parent::__construct(400, $message, $code, $previous);
    }
}
