<?php
/**
 * Created by PhpStorm.
 * User: nik
 * Date: 10.06.19
 * Time: 12:49
 */

namespace Zvinger\BaseClasses\app\modules\api\admin\v1\components\user\models;


use yii\base\BaseObject;

class UserApiAdminV1ModelList extends BaseObject
{
    /**
     * @var UserApiAdminV1Model[]
     * @SWG\Property()
     *
     */

    public $userModels = [];
}
