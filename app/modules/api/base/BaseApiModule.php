<?php

namespace Zvinger\BaseClasses\app\modules\api\base;

use Zvinger\BaseClasses\app\modules\api\ApiModule;
use Zvinger\BaseClasses\app\modules\api\base\components\LoginComponent;
use Zvinger\BaseClasses\app\modules\api\base\components\RegistrationComponent;

/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 01.12.17
 * Time: 22:47
 */
class BaseApiModule extends ApiModule
{
    public $registrationConfig;

    public $authConfig;

    public function init()
    {
        $this->components = [
            'registrationComponent' => [
                'class' => RegistrationComponent::class,
                'recaptcha' => $this->registrationConfig['recaptcha'] ?: false
            ],
            'loginComponent' => [
                'class' => LoginComponent::class,
                'google2FA' => $this->authConfig['google2FA'] ?: false
            ]
        ];
        parent::init();
    }

}
