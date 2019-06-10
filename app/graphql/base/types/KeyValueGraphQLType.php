<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 10.03.2019
 * Time: 23:00
 */

namespace Zvinger\BaseClasses\app\graphql\base\types;


use GraphQL\Type\Definition\Type;
use Zvinger\BaseClasses\app\graphql\base\BaseGraphQLObjectType;

class KeyValueGraphQLType extends BaseGraphQLObjectType
{
    public function __construct(
        $keyField = 'key',
        $valueField = 'value',
        $keyTypeDefinition = null,
        $valueTypeDefinition = null
    ) {
        if (is_null($valueTypeDefinition)) {
            $valueTypeDefinition = Type::string();
        }
        if (is_null($keyTypeDefinition)) {
            $keyTypeDefinition = Type::string();
        }
        $config = [
            'fields' => function () use ($keyField, $valueField, $keyTypeDefinition, $valueTypeDefinition) {
                return [
                    $keyField => $keyTypeDefinition,
                    $valueField => $valueTypeDefinition,
                ];
            },
        ];

        parent::__construct($config);
    }
}
