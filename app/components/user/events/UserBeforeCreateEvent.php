<?php
/**
 * Created by PhpStorm.
 * User: nik
 * Date: 17.05.19
 * Time: 10:28
 */

namespace Zvinger\BaseClasses\app\components\user\events;


use yii\base\Event;

class UserBeforeCreateEvent extends Event
{
    public $special;

    public $username;

    public $password;

    public $email;
}
