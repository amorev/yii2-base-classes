<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 20.08.18
 * Time: 17:54
 */

namespace Zvinger\BaseClasses\app\components\data\filterInfo;

use yii\base\BaseObject;
use yii\base\InvalidCallException;
use yii\base\UnknownPropertyException;
use yii\db\Expression;
use yii\db\Query;
use Zvinger\BaseClasses\app\components\data\filterInfo\models\BaseFilterElement;
use Zvinger\BaseClasses\app\components\data\filterInfo\models\DBFilterMiscInfo;
use Zvinger\BaseClasses\app\components\data\filterInfo\models\SimpleValueElement;

class VendorFilterInfoService extends BaseObject
{
    /**
     * @var int
     */
    private $object_id;

    /**
     * @var string
     */
    protected static $object_type;

    public function __construct(int $object_id, string $object_type = null, $config = [])
    {
        parent::__construct($config);
        $this->object_id = $object_id;
        static::$object_type = $object_type;
    }

    /**
     * @return int
     */
    public function getObjectId(): int
    {
        return $this->object_id;
    }

    /**
     * @return string
     */
    public function getObjectType(): string
    {
        return static::$object_type;
    }

    protected function setObjectDataByKey(string $name, BaseFilterElement $value)
    {
        $value->setElementValue($name, $this);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return
     */
    public static function getFilterIdsQuery(array $elements = [], $allIds = [])
    {
        $query = DBFilterMiscInfo::find();
        $keys = array_keys($elements);
        $arr = [
            'and',
        ];
        if ($allIds) {
            $arr[] = ['object_id' => $allIds];
        }
        $arr = array_merge(
            $arr,
            [
                ['object_type' => static::$object_type],
                ['key' => $keys],
            ]
        );
        $query
            ->select(['object_id'])
            ->where($arr)
            ->groupBy(['object_id', 'key', 'value_id', 'value_data'])
            ->orderBy(new Expression('null'));
//        d($query->createCommand()->rawSql);
//        die;
        $havingCondition = ['or'];
        foreach ($elements as $key => $element) {
            $havingCondition[] = ['and', ['key' => $key], $element];
        }
        $query->having($havingCondition);
        $queryIds = new Query();
        $queryIds->from(['filter' => $query])
            ->select(['object_id'])
            ->groupBy(['object_id'])
            ->having(['count(object_id)' => count($keys)])
            ->orderBy(new Expression('null'));
        $objectIds = $queryIds->column();

        return $objectIds;
    }

    public function setSimple($key, $value, $isDictionary)
    {
        return $this->setObjectDataByKey(
            $key,
            new SimpleValueElement(
                [ //  тут пример для информации которая сохраняется в виде единичного экземпляра
                    'value' => $value,
                    'isDictionary' => $isDictionary, //Если мы привязываемся к инфе из dictionary_info
                ]
            )
        );
    }

    public function getData($key)
    {
        $query = DBFilterMiscInfo::find()
            ->where(
                [
                    'and',
                    ['object_type' => static::$object_type],
                    ['object_id' => $this->object_id],
                    ['key' => $key],
                ]
            );
        $objects = $query->all();

        return $objects;
    }

    public function getValue($key, $isArray = false)
    {
        $objects = $this->getData($key);
        $result = null;
        foreach ($objects as $object) {
            $resultValue = $object->value_id ?: $object->value_data;
            if ($isArray) {
                $result[] = $resultValue;
            } else {
                $result = $resultValue;
                break;
            }
        }

        return $result;
    }
}
