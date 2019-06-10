<?php
/**
 * Created by PhpStorm.
 * User: nik
 * Date: 16.05.19
 * Time: 11:03
 */

namespace Zvinger\BaseClasses\app\modules\api\base\requests\registration;

use Zvinger\BaseClasses\api\request\BaseApiRequest;

/**
 * Class CreateUserRequest
 * @package Zvinger\BaseClasses\app\modules\api\base\requests\registration
 * @SWG\Definition()
 */

class RegistrationRequest extends BaseApiRequest
{
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
     * @var string
     * @SWG\Property()
     */
    public $login;

    /**
     * @var array
     * @SWG\Property()
     */
    public $special;

}
