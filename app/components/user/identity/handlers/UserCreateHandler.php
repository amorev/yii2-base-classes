<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 21.12.17
 * Time: 12:47
 */

namespace Zvinger\BaseClasses\app\components\user\identity\handlers;

use yii\base\Event;
use Zvinger\BaseClasses\app\components\user\exceptions\UserCreateException;
use Zvinger\BaseClasses\app\components\user\identity\events\EventActivationUpdated;
use Zvinger\BaseClasses\app\exceptions\model\ModelValidateException;
use Zvinger\BaseClasses\app\helpers\error\ErrorMessageHelper;
use app\models\work\user\object\UserObject;
use yii\base\BaseObject;

class UserCreateHandler extends BaseObject
{
    private $_username;

    private $_password;

    private $_email;

    /**
     * @param mixed $username
     * @return UserCreateHandler
     */
    public function setUsername($username)
    {
        $this->_username = $username;

        return $this;
    }

    /**
     * @param mixed $password
     * @return UserCreateHandler
     */
    public function setPassword($password)
    {
        $this->_password = $password;

        return $this;
    }

    /**
     * @param mixed $email
     * @return UserCreateHandler
     */
    public function setEmail($email)
    {
        $this->_email = $email;

        return $this;
    }

    /**
     * @throws \Exception
     * @throws UserCreateException
     * @return UserObject
     */
    public function createUser()
    {
        if (empty($this->_email) || empty($this->_password)) {
            throw new \Exception("No full data for creating user");
        }
        $this->_username = $this->_username ?: $this->_email;
        $user = new UserObject();
        $user->username = $this->_username;
        $user->email = $this->_email;
        $user->password = $this->_password;
        $user->validate();
        if ($user->firstErrors){
            throw new ModelValidateException($user);
        }
        $user->save();
        return $user;
    }
}
