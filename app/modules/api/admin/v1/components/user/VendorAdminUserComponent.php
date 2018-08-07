<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 21.02.18
 * Time: 17:21
 */

namespace Zvinger\BaseClasses\app\modules\api\admin\v1\components\user;

use app\models\work\user\object\UserObject;
use yii\base\Event;
use yii\rbac\Role;
use Zvinger\BaseClasses\app\exceptions\model\ModelValidateException;
use Zvinger\BaseClasses\app\models\work\user\object\VendorUserObject;
use Zvinger\BaseClasses\app\modules\api\admin\v1\actions\user\create\UserCreateRequest;
use Zvinger\BaseClasses\app\modules\api\admin\v1\actions\user\update\UserUpdateRequest;
use Zvinger\BaseClasses\app\modules\api\admin\v1\AdminApiVendorModule;
use Zvinger\BaseClasses\app\modules\api\admin\v1\components\user\models\UserApiAdminV1Model;
use Zvinger\BaseClasses\app\modules\api\admin\v1\components\user\models\UserSetInfo;
use Zvinger\BaseClasses\app\modules\api\admin\v1\events\AdminUserBeforeSendEvent;

class VendorAdminUserComponent
{

    /**
     * @param VendorUserObject $userObject
     * @return UserApiAdminV1Model
     */
    public function convertUserObjectToModel(VendorUserObject $userObject)
    {
        $userApiAdminV1Model = new UserApiAdminV1Model([
            'username' => $userObject->username,
            'email'    => $userObject->email,
            'id'       => $userObject->id,
            'status'   => $userObject->status,
            'roles'    => $this->getUserRoles($userObject->id),
        ]);
        $event = new AdminUserBeforeSendEvent();
        $event->userModel = &$userApiAdminV1Model;
        AdminApiVendorModule::getInstance()->trigger(AdminApiVendorModule::EVENT_USER_BEFORE_SEND, $event);

        return $userApiAdminV1Model;
    }

    /**
     * @param VendorUserObject[] $userObjects
     * @return UserApiAdminV1Model[]
     */
    public function convertUserObjectsToModelMultiple(array $userObjects)
    {
        $result = [];
        foreach ($userObjects as $userObject) {
            $result[] = $this->convertUserObjectToModel($userObject);
        }

        return $result;
    }

    public function convertUserIdToModel(int $userId)
    {
        return $this->convertUserObjectToModel(UserObject::findOne($userId));
    }

    /**
     * @param int $userId
     * @param UserUpdateRequest $userUpdateRequest
     * @return bool
     * @throws \Exception
     */
    public function updateUser(int $userId, UserUpdateRequest $userUpdateRequest): bool
    {
        $userObject = $this->getUserObject($userId);
        $userSetInfo = new UserSetInfo();
        foreach ($userUpdateRequest as $key => $value) {
            $userSetInfo->{$key} = $value;
        }
        $this->setUserInfo($userObject, $userSetInfo);
        if (!$userObject->save()) {
            throw new ModelValidateException($userObject);
        }
        $this->setUserRoles($userObject, $userSetInfo->roles);

        return true;
    }

    /**
     * @param UserCreateRequest $userCreateRequest
     * @return int
     * @throws ModelValidateException
     * @throws \Exception
     */
    public function createUser(UserCreateRequest $userCreateRequest): int
    {
        $userObject = new UserObject();
        $userSetInfo = new UserSetInfo();
        foreach ($userCreateRequest as $key => $value) {
            $userSetInfo->{$key} = $value;
        }
        $this->setUserInfo($userObject, $userSetInfo);

        if (!$userObject->save()) {
            throw new ModelValidateException($userObject);
        }
        $this->setUserRoles($userObject, $userSetInfo->roles);

        return $userObject->id;
    }

    /**
     * @param UserObject $userObject
     * @param UserSetInfo $userSetInfo
     * @throws \Exception
     */
    private function setUserInfo(UserObject &$userObject, UserSetInfo $userSetInfo)
    {
        $userObject->username = $userSetInfo->username;
        $userObject->email = $userSetInfo->email;
        if (!empty($userSetInfo->password)) {
            $userObject->password = $userSetInfo->password;
        }

    }

    /**
     * @param UserObject $userObject
     * @param array $roles
     * @throws \Exception
     */
    private function setUserRoles(UserObject &$userObject, array $roles)
    {
        $authManager = \Yii::$app->authManager;
        $authManager->revokeAll($userObject->id);
        foreach ($roles as $role) {
            $roleObject = $authManager->getRole($role);
            if (!empty($roleObject)) {
                $authManager->assign($roleObject, $userObject->id);
            }
        }
    }

    private function getUserRoles($userId)
    {
        $authManager = \Yii::$app->authManager;
        $rolesByUser = $authManager->getRolesByUser($userId);
        $result = [];
        foreach ($rolesByUser as $item) {
            $result[] = $item->name;
        }

        return $result;
    }

    private function getUserObject($userId)
    {
        return UserObject::findOne($userId);
    }
}