<?php
/**
 * Created by PhpStorm.
 * User: nik
 * Date: 20.05.19
 * Time: 11:19
 */

namespace Zvinger\BaseClasses\app\modules\api\base\requests\auth;


use Zvinger\BaseClasses\api\request\BaseApiRequest;

class LoginRequest extends BaseApiRequest
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

    public $password;

    /**
     * @var array
     * @SWG\Property()
     */

    public $special;
}
