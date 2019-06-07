<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 16.03.2019
 * Time: 23:41
 */

namespace Zvinger\BaseClasses\app\graphql\base\resolver;


use GraphQL\Type\Definition\ResolveInfo;
use yii\helpers\ArrayHelper;
use Zvinger\BaseClasses\app\graphql\base\context\BaseGraphQLContext;

abstract class BaseFieldResolver
{
    abstract public static function resolveCurrent($value, $args, BaseGraphQLContext $context, ResolveInfo $info);

    final public static function resolve($value, $args, BaseGraphQLContext $context, ResolveInfo $info)
    {
        static::validateArgs($value, $args, $context, $info);
        try {
            $result = static::resolveByFunction($info, $value);
        } catch (\Exception $e) {
        }
        if ($result instanceof NotResolved) {
            try {
                $result = static::resolveByMap($info, $value);
            } catch (\Exception $e) {
            }
        }
        if ($result instanceof NotResolved) {
            $result = static::resolveCurrent($value, $args, $context, $info);
        }

        return $result;
    }

    protected static function validateArgs($value, $args, BaseGraphQLContext $context, ResolveInfo $info)
    {
        return true;
    }

    protected static function resolveByMap(ResolveInfo $info, $value)
    {
        $getString = ArrayHelper::getValue(static::getMap(), $info->fieldName, $info->fieldName);
        if ($getString) {
            return ArrayHelper::getValue($value, $getString, new NotResolved());
        }

        return new NotResolved();
    }

    protected static function resolveByFunction(ResolveInfo $info, $value)
    {
        if (method_exists(static::class, 'resolve'.$info->fieldName)) {
            $callUserFunc = call_user_func([static::class, 'resolve'.$info->fieldName], $value);

            return $callUserFunc;
        }

        return new NotResolved();
    }

    public static function resolveCallback(): callable
    {
        return [static::class, 'resolve'];
    }

    protected static function getMap()
    {
        return [];
    }
}
