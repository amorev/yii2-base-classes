<?php
/**
 * Created by PhpStorm.
 * User: nik
 * Date: 20.05.19
 * Time: 15:48
 */

namespace Zvinger\BaseClasses\app\modules\api\base\responses\registration;


class RegistrationResponse
{

    /**
     * @var boolean
     * @SWG\Property()
     */

    public $status;

    /**
     * @var string
     * @SWG\Property()
     */

    public $token;
}
