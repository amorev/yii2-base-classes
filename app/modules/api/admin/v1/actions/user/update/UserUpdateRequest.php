<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 24.04.18
 * Time: 12:53
 */

namespace Zvinger\BaseClasses\app\modules\api\admin\v1\actions\user\update;

/**
 * Class UserUpdateRequest
 * @package Zvinger\BaseClasses\app\modules\api\admin\v1\actions\user\update
 * @SWG\Definition()
 */
class UserUpdateRequest
{
    /**
     * @var string
     * @SWG\Property()
     */
    public $username;

    /**
     * @var string
     * @SWG\Property()
     */
    public $email;

    /**
     * @var string
     * @SWG\Property()
     */
    public $password;

    /**
     * @var string[]
     * @SWG\Property()
     * Идентификаторы ролей
     */
    public $roles = [];
}