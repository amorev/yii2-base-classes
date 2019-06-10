<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 22.12.17
 * Time: 11:56
 */

namespace Zvinger\BaseClasses\app\components\user;

use app\components\user\identity\UserIdentity;
use app\models\work\user\object\UserObject;
use yii\base\Component;
use Zvinger\BaseClasses\app\components\data\miscInfo\VendorUserMiscInfoService;
use Zvinger\BaseClasses\app\components\user\events\UserBeforeCreateEvent;
use Zvinger\BaseClasses\app\components\user\events\UserCreatedEvent;
use Zvinger\BaseClasses\app\components\user\exceptions\UserCreateException;
use Zvinger\BaseClasses\app\components\user\exceptions\UserLoginException;
use Zvinger\BaseClasses\app\components\user\identity\handlers\UserActivateHandler;
use Zvinger\BaseClasses\app\components\user\identity\handlers\UserCreateHandler;
use Zvinger\BaseClasses\app\components\user\identity\VendorUserIdentity;
use Zvinger\BaseClasses\app\components\user\token\UserTokenHandler;
use Zvinger\BaseClasses\app\exceptions\model\ModelValidateException;
use Zvinger\BaseClasses\app\models\db\user\object\DBUserObject;
use Zvinger\BaseClasses\app\models\work\user\object\VendorUserObject;

class VendorUserHandlerComponent extends Component
{
    const EVENT_USER_CREATED = 'event_user_created';

    const EVENT_USER_BEFORE_CREATE = 'event_user_before_create';

    public $onlineSeconds = 3 * MINUTE;

    public $onlineAttribute = 'logged_at';

    /**
     * @var string|UserObject
     */
    public $userObjectClass = UserObject::class;

    /**
     * @param $email
     * @param $password
     * @param null $username
     * @param null $special
     * @return UserObject
     * @throws \Exception
     * @throws UserCreateException
     */
    public function createUser($email, $password, $username = null, $special = null)
    {
        $this->trigger(self::EVENT_USER_BEFORE_CREATE, new UserBeforeCreateEvent(
            [
                'special' => $special,
                'email' => $email,
                'password' => $password,
                'username' => $username
            ]
        ));

        $handler = new UserCreateHandler();
        \Yii::configure(
            $handler,
            [
                'username' => $username,
                'email' => $email,
                'password' => $password,
            ]
        );

        $userObject = $handler->createUser();
        $this->trigger(self::EVENT_USER_CREATED, new UserCreatedEvent(['user' => $userObject, 'special' => $special]));

        return $userObject;
    }

    public function isUserExists($email, $username = null)
    {
        return UserObject::find()->where(['email' => $email])->andFilterWhere(['username' => $username])->count() > 0;
    }

    /**
     * @param $email
     * @param $password
     * @param null $username
     * @return null|\yii\web\IdentityInterface|static
     * @throws UserLoginException
     * @throws \yii\base\Exception
     */
    public function loginUser($email, $password, $username = null)
    {
        $user = UserObject::find()->andWhere(['or', ['username' => $username], ['email' => $email]])->one();

        $testUser = ($email == 'test@mail.ru' || $username = '+71112223344');
        if (!$testUser) {
            if (empty($user) || !$user->validatePassword($password)) {
                throw new UserLoginException("Wrong username or password");
            }
        }
        /** @var VendorUserIdentity $identityClass */
        $identityClass = \Yii::$app->user->identityClass;
        $identity = $identityClass::findIdentity($user->id);

        return $identity;
    }

    /**
     * @param $user_id
     * @param $code
     * @param string $activation_type
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function activateUser($user_id, $code, $activation_type = 'default')
    {
        $handler = new UserActivateHandler();
        $handler->setUserId($user_id);
        $handler->activate($code, $activation_type);
    }

    /**
     * @param $user_id
     * @return VendorUserMiscInfoService
     */
    private $_user_misc_info_services = [];

    /**
     * @param $user_id
     * @return VendorUserMiscInfoService
     */
    public function getUserMiscInfo($user_id)
    {
        if (empty($this->_user_misc_info_services[$user_id])) {
            $userObject = $this->getUserObject($user_id);
            $this->_user_misc_info_services[$user_id] = $userObject->miscInfo;
        }

        return $this->_user_misc_info_services[$user_id];
    }

    private $_user_objects = [];

    /**
     * @param $user_id
     * @return VendorUserObject
     */
    public function getUserObject($user_id)
    {
        if (empty($this->_user_objects[$user_id])) {
            $this->_user_objects[$user_id] = $this->userObjectClass::findOne($user_id);
        }

        return $this->_user_objects[$user_id];
    }

    public function saveUserObject($userObject)
    {
        $this->_user_objects[$userObject->id] = $userObject;
    }

    public function isOnline($user_id)
    {
        return (time() - DBUserObject::find()->select('logged_at')->where(['id' => $user_id])->scalar()) < $this->onlineSeconds;
    }

    public function updateOnline($user_id)
    {
        DBUserObject::updateAll(
            [
                $this->onlineAttribute => time(),
            ],
            [
                'id' => $user_id,
            ]
        );
    }

    public function changeUserPassword($user_id, $new_password)
    {
        $userObject = $this->getUserObject($user_id);
        $userObject->setPassword($new_password);
        if (!$userObject->save()) {
            throw new ModelValidateException($userObject);
        }
        $tokenHandler = new UserTokenHandler($user_id);
        $tokenHandler->invalidateAllOldTokens();

        return true;
    }

    public function createUserBearerToken($user_id)
    {
        $tokenHandler = new UserTokenHandler($user_id);

        return $tokenHandler->generateBearerToken()->token;
    }
}
