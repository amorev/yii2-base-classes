<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 15.03.2019
 * Time: 2:20
 */

namespace Zvinger\BaseClasses\app\graphql\base\types\input;


use GraphQL\Type\Definition\InputObjectType;
use Zvinger\BaseClasses\app\graphql\base\traits\ObjectTypeTrait;

class BaseGraphQLInputType extends InputObjectType
{
    use ObjectTypeTrait;
}
