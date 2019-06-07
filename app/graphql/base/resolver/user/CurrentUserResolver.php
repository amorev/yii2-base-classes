<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 16.03.2019
 * Time: 23:44
 */

namespace Zvinger\BaseClasses\app\graphql\base\resolver\user;


use GraphQL\Type\Definition\ResolveInfo;
use Zvinger\BaseClasses\app\graphql\base\context\BaseGraphQLContext;
use Zvinger\BaseClasses\app\graphql\base\resolver\BaseFieldResolver;

class CurrentUserResolver extends BaseFieldResolver
{
    public static function resolveCurrent($value, $args, BaseGraphQLContext $context, ResolveInfo $info)
    {
        $userObject = $context->getIdentity()->getUserObject();

        return $userObject;
    }
}
