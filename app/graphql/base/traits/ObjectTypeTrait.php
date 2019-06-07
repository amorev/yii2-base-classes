<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 15.03.2019
 * Time: 2:20
 */

namespace Zvinger\BaseClasses\app\graphql\base\traits;


use GraphQL\Type\Definition\ObjectType;
use Zvinger\BaseClasses\app\graphql\helpers\VendorTypesCollection;

trait ObjectTypeTrait
{
    /**
     * @param array $constructorVars
     * @return ObjectType
     */
    public static function initType($constructorVars = [], $name = null)
    {
        return VendorTypesCollection::getField(static::class, $name, $constructorVars);
    }
}
