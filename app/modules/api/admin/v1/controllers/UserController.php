<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 20.12.17
 * Time: 19:19
 */

namespace Zvinger\BaseClasses\app\modules\api\admin\v1\controllers;

use app\models\work\user\object\UserObject;
use yii\web\BadRequestHttpException;
use Zvinger\BaseClasses\app\models\work\user\object\VendorUserObject;
use Zvinger\BaseClasses\app\modules\api\admin\v1\actions\user\create\UserCreateRequest;
use Zvinger\BaseClasses\app\modules\api\admin\v1\actions\user\update\UserUpdateRequest;
use Zvinger\BaseClasses\app\modules\api\admin\v1\AdminApiVendorModule;
use Zvinger\BaseClasses\app\modules\api\admin\v1\components\user\models\UserApiAdminV1Model;
use Zvinger\BaseClasses\app\modules\api\admin\v1\components\user\models\UserApiAdminV1ModelList;
use Zvinger\BaseClasses\app\modules\api\admin\v1\controllers\base\BaseVendorAdminV1Controller;
use Zvinger\BaseClasses\app\modules\api\admin\v1\events\AdminUserSavedEvent;

class UserController extends BaseVendorAdminV1Controller
{
    public function actionIndex()
    {
        return $this->module->userComponent->convertUserObjectsToModelMultiple(VendorUserObject::find()->all());
    }

    /**
     * @param $id
     * @return UserApiAdminV1Model
     * @SWG\GET(path="/vendor/users/{userId}",
     *     tags={"user"},
     *     summary="Получение пользователя",
     *     @SWG\Parameter(
     *          in="path",
     *          name="userId",
     *          type="integer",
     *          required=true
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Модель пользователя",
     *         @SWG\Schema(ref = "#/definitions/UserApiAdminV1Model")
     *     ),
     * )
     */
    public function actionView($id)
    {
        $userApiAdminV1Model = $this->module->userComponent->convertUserObjectToModel(UserObject::findOne($id));

        return $userApiAdminV1Model;
    }


    public function actionList()
    {
        $userApiAdminV1ModelList = $this->module->userComponent->convertUserObjectsToModels(UserObject::find()->all());

        return $userApiAdminV1ModelList;
    }

    /**
     * @param $id
     * @return object
     * @throws BadRequestHttpException
     * @throws \Exception
     * @SWG\Put(path="/vendor/users/{userId}",
     *     tags={"user"},
     *     summary="Обновление пользователя",
     *     @SWG\Parameter(
     *          in="path",
     *          name="userId",
     *          type="integer",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          @SWG\Schema(ref = "#/definitions/UserUpdateRequest")
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Модель пользователя",
     *         @SWG\Schema(ref = "#/definitions/UserApiAdminV1Model")
     *     ),
     * )
     */
    public function actionUpdate($id)
    {
        $user = UserObject::findOne($id);
        /** @var UserUpdateRequest $request */
        $request = \Yii::configure(new UserUpdateRequest(), \Yii::$app->request->post());
        $this->module->userComponent->updateUser($id, $request);
        AdminApiVendorModule::getInstance()->trigger(AdminApiVendorModule::EVENT_USER_SAVED, new AdminUserSavedEvent([
            'userId' => $id,
            'request' => $request,
        ]));

        return $this->module->userComponent->convertUserObjectToModel($user);
    }

    /**
     * @return UserApiAdminV1Model
     * @throws \Zvinger\BaseClasses\app\exceptions\model\ModelValidateException
     * @SWG\Post(path="/vendor/users",
     *     tags={"user"},
     *     summary="Создание пользователя",
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          @SWG\Schema(ref = "#/definitions/UserCreateRequest")
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Модель пользователя",
     *         @SWG\Schema(ref = "#/definitions/UserApiAdminV1Model")
     *     ),
     * )
     */
    public function actionCreate()
    {
        $request = UserCreateRequest::createRequest();
        $userId = $this->module->userComponent->createUser($request);
        AdminApiVendorModule::getInstance()->trigger(AdminApiVendorModule::EVENT_USER_SAVED, new AdminUserSavedEvent([
            'userId' => $userId,
            'request' => $request,
        ]));

        return $this->module->userComponent->convertUserIdToModel($userId);
    }
}
