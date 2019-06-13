<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 07.06.2019
 * Time: 13:17
 */

namespace Zvinger\BaseClasses\app\modules\api\base\controllers;


use Obvu\Modules\Api\Admin\controllers\base\BaseAdminController;
use Zvinger\BaseClasses\app\modules\api\base\components\handlers\userInformation\UserInformationHandlerInterface;
use Zvinger\BaseClasses\app\modules\api\base\components\handlers\userInformation\UserSettingsHandlerInterface;

class UserSettingsController extends BaseAdminController
{
    public function actionGetCurrentUser()
    {
        return \Yii::createObject(UserInformationHandlerInterface::class)->getMainUserInformation(
            \Yii::$app->user->id
        );
    }
    
    public function actionConfiguration()
    {
        return \Yii::createObject(UserSettingsHandlerInterface::class)->getCurrentUserSettings();
    }

    public function actionGetCurrentData()
    {
        return \Yii::createObject(UserInformationHandlerInterface::class)->getUserInformation(
            \Yii::$app->user->id
        );
    }

    public function actionSaveCurrentData()
    {
        return [
            'saved' => \Yii::createObject(UserInformationHandlerInterface::class)->saveUserInformation(
                \Yii::$app->user->id,
                \Yii::$app->request->post('userData')
            ),
        ];
    }
}
