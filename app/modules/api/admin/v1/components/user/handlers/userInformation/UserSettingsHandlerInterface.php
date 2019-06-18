<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 07.06.2019
 * Time: 13:33
 */

namespace Zvinger\BaseClasses\app\modules\api\admin\v1\components\user\handlers\userInformation;


use Zvinger\BaseClasses\app\modules\api\base\components\handlers\userInformation\models\settings\UserSettingsModel;

interface UserSettingsHandlerInterface
{
    public function getCurrentUserSettings(): UserSettingsModel;

}
