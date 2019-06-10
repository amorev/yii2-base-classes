<?php

namespace Zvinger\BaseClasses\app\components\data\dictionary\models;

use Yii;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "dictionary_info".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $title
 * @property string $description
 * @property string $slug
 * @property int $sort
 * @property int $fixed
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class DBDictionaryInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dictionary_info';
    }

    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'sort', 'fixed', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['description'], 'string'],
            [['title', 'slug'], 'string', 'max' => 255],
            [
                ['parent_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => DBDictionaryInfo::className(),
                'targetAttribute' => ['parent_id' => 'id'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'title' => 'Title',
            'description' => 'Description',
            'sort' => 'Sort',
            'fixed' => 'Fixed',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];
    }
}
