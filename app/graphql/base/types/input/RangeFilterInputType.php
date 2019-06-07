<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 27.03.2019
 * Time: 13:40
 */

namespace Zvinger\BaseClasses\app\graphql\base\types\input;


use GraphQL\Type\Definition\Type;

class RangeFilterInputType extends BaseGraphQLInputType
{
    public function __construct($config = [])
    {
        $config = [
            'fields' => [
                'min' => Type::int(),
                'max' => Type::int(),
            ],
        ];
        parent::__construct($config);
    }
}
