<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 07.06.2019
 * Time: 13:27
 */

namespace Zvinger\BaseClasses\app\modules\api\base\components\handlers\userInformation;


use yii\web\NotFoundHttpException;
use Zvinger\BaseClasses\app\models\work\user\object\VendorUserObject;
use Zvinger\BaseClasses\app\modules\api\base\BaseApiModule;
use Zvinger\BaseClasses\app\modules\api\base\components\handlers\userInformation\models\information\MainUserInformationModel;
use Zvinger\BaseClasses\app\modules\api\base\components\handlers\userInformation\models\information\UserInformationModel;
use Zvinger\BaseClasses\app\modules\api\base\components\handlers\userInformation\models\settings\UserSettingsModel;

class SimpleUserInformationSavingHandler implements
    UserSettingsHandlerInterface,
    UserInformationHandlerInterface
{

    public function getUserInformation(int $userId): UserInformationModel
    {
        $userObject = $this->getUserObject($userId);
        $key = $this->getMiscInfoKey();
        $data = new UserInformationModel();
        $data->userData = $userObject->miscInfo->getNoCheck($key) ?? new \stdClass();

        return $data;
    }

    public function getCurrentUserSettings(): UserSettingsModel
    {
        return $this->getModule()->userInformationConfiguration;
    }

    public function saveUserInformation(int $userId, $data = []): bool
    {
        $userObject = $this->getUserObject($userId);
        $userObject->miscInfo->{$this->getMiscInfoKey()} = $data;

        return true;
    }

    public function getMainUserInformation(int $userId): MainUserInformationModel
    {
        $userObject = $this->getUserObject($userId);
        $minUserInformationModel = \Yii::createObject(
            [
                'class' => MainUserInformationModel::class,
                'email' => $userObject->email,
                'username' => $userObject->username,
                'isAdmin' => \Yii::$app->user->can('admin')
            ]
        );
        return $minUserInformationModel;
    }

    /**
     * @param int $userId
     * @return VendorUserObject
     * @throws NotFoundHttpException
     */
    protected function getUserObject(int $userId): VendorUserObject
    {
        $static = $this->getModule();
        /** @var VendorUserObject $userObject */
        $userObject = $static->userObjectClass::findOne($userId);
        if (!$userObject) {
            throw new NotFoundHttpException('User not found');
        }

        return $userObject;
    }

    /**
     * @return BaseApiModule|null
     */
    protected function getModule()
    {
        return BaseApiModule::getInstance();
    }

    /**
     * @return string
     */
    protected function getMiscInfoKey(): string
    {
        return $this->getModule()->miscInfoKeyForUserDataInformation;
    }


}
