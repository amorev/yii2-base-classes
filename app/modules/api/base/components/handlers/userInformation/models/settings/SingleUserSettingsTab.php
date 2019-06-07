<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 07.06.2019
 * Time: 13:30
 */

namespace Zvinger\BaseClasses\app\modules\api\base\components\handlers\userInformation\models\settings;


use Obvu\Modules\Api\Admin\submodules\crud\components\settings\models\entity\blocks\base\BaseEditDataBlock;
use Obvu\Modules\Api\Admin\submodules\crud\components\settings\models\entity\fields\base\BaseCrudSingleField;

class SingleUserSettingsTab
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $name;

    /**
     * @var BaseCrudSingleField|BaseEditDataBlock
     */
    public $fields = [];
}
