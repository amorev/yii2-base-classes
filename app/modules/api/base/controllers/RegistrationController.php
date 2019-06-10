<?php
/**
 * Created by PhpStorm.
 * User: nik
 * Date: 16.05.19
 * Time: 11:01
 */

namespace Zvinger\BaseClasses\app\modules\api\base\controllers;

use yii\web\Controller;
use Zvinger\BaseClasses\api\controllers\BaseApiController;
use Zvinger\BaseClasses\app\modules\api\base\requests\registration\RegistrationRequest;


class RegistrationController extends BaseApiController
{
    public function actionIndex()
    {
        $requst = new RegistrationRequest();
        return $this->module->registrationComponent->run($requst::createRequest());
    }

}
