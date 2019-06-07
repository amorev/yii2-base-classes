<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 22.12.17
 * Time: 12:01
 */

namespace Zvinger\BaseClasses\app\components\user\events;

use yii\base\Event;
use Zvinger\BaseClasses\app\models\work\user\object\VendorUserObject;

class UserCreatedEvent extends Event
{
    /**
     * @var VendorUserObject
     */
    public $user;

    /**
     * @var VendorUserObject
     */
    public $special = null;


}
