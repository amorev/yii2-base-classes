<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 30.08.18
 * Time: 13:01
 */

namespace Zvinger\BaseClasses\app\components\data\filterInfo;


use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use Zvinger\BaseClasses\app\components\data\dictionary\models\DBDictionaryInfo;
use Zvinger\BaseClasses\app\components\data\filterInfo\models\SingleDictionaryElement;
use Zvinger\BaseClasses\app\components\data\filterInfo\models\SingleDictionaryElementForSelect;
use Zvinger\BaseClasses\app\exceptions\model\ModelValidateException;

class VendorFilterInfoComponent
{
    const FIELD_TYPE_DATA = 'data';
    const FIELD_TYPE_DICTIONARY = 'dictionary';

    /**
     * @var SingleDictionaryElement[]
     */
    public $dictionary = [];

    public $types = [];

    public function initDictionary($interactive = true)
    {
        $cleanDictionary = $interactive ? Console::confirm("Clean old dictionary?", true) : true;
        if ($cleanDictionary) {
            DBDictionaryInfo::deleteAll([]);
            \Yii::$app->db->createCommand('ALTER TABLE ' . DBDictionaryInfo::tableName() . ' AUTO_INCREMENT = 1')->execute();
        }
        foreach ($this->dictionary as $item) {
            $this->handleDictionaryElement($item);
        }
    }

    private function handleDictionaryElement($element)
    {
        $element = $this->prepareElement($element);
        $object = new DBDictionaryInfo([
            'id'          => $element->id,
            'title'       => $element->title,
            'description' => $element->description,
            'parent_id'   => $element->parentId,
            'fixed'       => true,
        ]);
        if (!empty($element->id)) {
            $duplicate = $this->objectById($element->id);
            if (!empty($duplicate)) {
                $duplicate->id = DBDictionaryInfo::find()->select('id')->orderBy(['id' => SORT_DESC])->scalar() + 1;
                \Yii::$app->db->createCommand('ALTER TABLE ' . DBDictionaryInfo::tableName() . ' AUTO_INCREMENT = ' . ($duplicate->id + 1))->execute();
                if (!$duplicate->save()) {
                    throw new ModelValidateException($duplicate);
                }
            }
        }
        if (!$object->save()) {
            throw new ModelValidateException($object);
        }
        if ($element->children) {
            foreach ($element->children as $child) {
                $child = $this->prepareElement($child);
                $child->parentId = $object->id;
                $this->handleDictionaryElement($child);
            }
        }
    }

    private function objectById($id)
    {
        return DBDictionaryInfo::findOne($id);
    }

    public function getDictionaryBlock($objectId)
    {
        $objects = DBDictionaryInfo::find()->where(['parent_id' => $objectId])->orderBy(['title' => 'SORT_ASC'])->all();

        return $objects;
    }

    public function getDictionaryBlockForSelect($objectId, $useSlug = true)
    {
        $objects = $this->getDictionaryBlock($objectId);
        $result = [];
        foreach ($objects as $object) {
            $result[] = \Yii::createObject([
                'class' => SingleDictionaryElementForSelect::class,
                'key'   => ($useSlug && $object->slug) ? $object->slug : $object->id,
                'value' => $object->title,
            ]);
        }

        return $result;
    }

    /**
     * @param $element
     * @return SingleDictionaryElement
     */
    private function prepareElement($element): SingleDictionaryElement
    {
        if (is_string($element)) {
            $element = new SingleDictionaryElement([
                'title' => $element,
            ]);
        }
        if (is_array($element)) {
            $element = new SingleDictionaryElement([
                'title' => $element['value'],
                'id'    => $element['key'],
            ]);
        }

        return $element;
    }

    public function getFieldType($key)
    {
        return ArrayHelper::getValue($this->types, $key, self::FIELD_TYPE_DICTIONARY);
    }
}
