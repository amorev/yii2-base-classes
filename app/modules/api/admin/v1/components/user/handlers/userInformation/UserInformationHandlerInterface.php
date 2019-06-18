<?php

namespace Zvinger\BaseClasses\app\modules\api\admin\v1\components\user\handlers\userInformation;

use Zvinger\BaseClasses\app\modules\api\base\components\handlers\userInformation\models\information\UserInformationModel;

interface UserInformationHandlerInterface
{
    public function getUserInformation(int $userId): UserInformationModel;

    public function saveUserInformation(int $userId, $data = []): bool ;
}
