<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 18.03.2019
 * Time: 0:06
 */

namespace Zvinger\BaseClasses\app\graphql\base\resolver\simple;


use GraphQL\Type\Definition\ResolveInfo;
use Zvinger\BaseClasses\app\graphql\base\context\BaseGraphQLContext;
use Zvinger\BaseClasses\app\graphql\base\resolver\BaseFieldResolver;

class ProxyFieldResolver extends BaseFieldResolver
{
    public static function resolveCurrent($value, $args, BaseGraphQLContext $context, ResolveInfo $info)
    {
        return $value;
    }
}
