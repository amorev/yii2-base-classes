<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 15.03.2019
 * Time: 2:13
 */

namespace Zvinger\BaseClasses\app\graphql\base\types\input;


use GraphQL\Type\Definition\Type;
use Zvinger\BaseClasses\app\graphql\base\BaseGraphQLObjectType;

class PaginationInputType extends BaseGraphQLInputType
{
    public function __construct()
    {
        $config = [
            'name' => 'pagination_type',
            'fields' => function () {
                return [
                    'page' => [
                        'type' => Type::int(),
                    ],
                    'perPage' => [
                        'type' => Type::int(),
                    ],
                ];
            },
        ];

        parent::__construct($config);
    }
}
