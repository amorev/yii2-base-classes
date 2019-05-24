<?php
/**
 * Created by PhpStorm.
 * User: nik
 * Date: 24.05.19
 * Time: 8:40
 */

namespace Zvinger\BaseClasses\app\graphql\base\types\query\fileInfo;


use GraphQL\Type\Definition\Type;
use Zvinger\BaseClasses\app\graphql\base\BaseGraphQLObjectType;

class FileInfoType extends BaseGraphQLObjectType
{

    public function __construct()
    {
        $config = [
            'fields' => function () {
                return [
                    'fileId' => [
                        'type' => Type::int(),
                        'resolve' => function ($id) {
                            return $id;
                        },
                    ],
                    'fileUrl' => [
                        'type' => Type::string(),
                        'resolve' => function ($id) {
                            $storage = \Yii::$app->getModule(FILE_STORAGE_MODULE)->storage;
                            if ($storage) {
                                $fileModel = $storage->getFile($id);
                                if ($fileModel)
                                    return $fileModel->getFullUrl();
                            }
                            return null;
                        },
                    ],
                ];
            },
        ];

        parent::__construct($config);
    }
}
