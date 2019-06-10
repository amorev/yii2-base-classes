<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 10.03.2019
 * Time: 14:50
 */

namespace Zvinger\BaseClasses\app\graphql\helpers;


use yii\base\Module;

class BaseTypesCollection
{
    private static $fields = [];

    public static function getField($class, $name = null, $constructVars = [])
    {
        $forNaming = [];
        foreach ($constructVars as $constructVar) {
            $forName = $constructVar;
            if ($constructVar instanceof Module) {
                $forName = $constructVar->getUniqueId() ?? get_class($constructVar);
            }
            $forNaming[] = $forName;
        }
        if (empty($name)) {
            $name = json_encode(serialize($forNaming));
        }
        $name = md5($class.$name);

        return self::$fields[$name] ?: (self::$fields[$name] = \Yii::createObject($class, $constructVars));
    }
}
