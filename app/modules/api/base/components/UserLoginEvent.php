<?php


namespace Zvinger\BaseClasses\app\modules\api\base\components;


use yii\base\Event;

class UserLoginEvent extends Event
{
    public $userId;
}
