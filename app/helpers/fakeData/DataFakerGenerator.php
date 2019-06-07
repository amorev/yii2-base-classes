<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 27.04.18
 * Time: 23:33
 */

namespace Zvinger\BaseClasses\app\helpers\fakeData;


use Faker\Factory;
use Faker\Generator;

class DataFakerGenerator
{
    /**
     * @var Generator
     */
    private static $faker;

    public static function go()
    {
        if (empty(static::$faker)) {
            static::$faker = Factory::create('ru_RU');
        }

        return static::$faker;
    }

    public static function fakeIt(&$object)
    {
        if ($object instanceof FakeFilledInterface) {
            $object->fillFakeData();
        }

        return $object;
    }

    public static function fakeArray($objectsClass, $min = 1, $max = 10)
    {
        $count = rand($min, $max);
        $i = $min;
        $result = [];
        while ($i < ($min+$count)) {
            $result[] = static::fakeIt(new $objectsClass());
            $i++;
        }

        return $result;
    }


    public static function fakeArrayByExample(callable $example, $min = 1, $max = 10)
    {
        $count = rand($min, $max);
        $i = $min;
        $result = [];
        while ($i < ($min+$count)) {
            $result[] = $example();
            $i++;
        }

        return $result;
    }
}
