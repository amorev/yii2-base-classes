<?php

namespace Zvinger\BaseClasses\app\modules\api\base;

use Zvinger\BaseClasses\app\models\work\user\object\VendorUserObject;
use Zvinger\BaseClasses\app\modules\api\ApiModule;
use Zvinger\BaseClasses\app\modules\api\base\components\handlers\userInformation\models\settings\UserSettingsModel;
use Zvinger\BaseClasses\app\modules\api\base\components\handlers\userInformation\SimpleUserInformationSavingHandler;
use Zvinger\BaseClasses\app\modules\api\base\components\handlers\userInformation\UserInformationHandlerInterface;
use Zvinger\BaseClasses\app\modules\api\base\components\handlers\userInformation\UserSettingsHandlerInterface;

/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 01.12.17
 * Time: 22:47
 */
class BaseApiModule extends ApiModule
{
    /**
     * @var VendorUserObject
     */
    public $userObjectClass;

    public $miscInfoKeyForUserDataInformation = 'userDataInformation';

    /**
     * @var UserSettingsModel
     */
    public $userInformationConfiguration = null;

    public $userInformationHandlerClass = SimpleUserInformationSavingHandler::class;
    public $userSettingsHandlerClass = SimpleUserInformationSavingHandler::class;

    public function init()
    {
        \Yii::$container->setDefinitions(
            [
                UserInformationHandlerInterface::class => $this->userInformationHandlerClass,
                UserSettingsHandlerInterface::class => $this->userSettingsHandlerClass,
            ]
        );
        parent::init();
    }


}
