<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 10.03.2019
 * Time: 22:53
 */

namespace Zvinger\BaseClasses\app\graphql\base;

use GraphQL\Type\Definition\ObjectType;
use Zvinger\BaseClasses\app\graphql\base\traits\ObjectTypeTrait;
use Zvinger\BaseClasses\app\graphql\helpers\VendorTypesCollection;

class BaseGraphQLObjectType extends ObjectType
{
    use ObjectTypeTrait;
}
