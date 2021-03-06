<?php

namespace Zvinger\BaseClasses\app\helpers;

use DateTime;

class FunctionsHelpers
{
    /**
     * @param bool $var
     * @param bool $showHtml
     * @param bool $showFrom
     * @param bool $overDebug
     * @return bool
     */
    public static function DebugObject($var = false, $showHtml = false, $showFrom = true, $overDebug = false, $calledFrom = null)
    {
        $PROJECT_DEBUG = YII_DEBUG || YII_ENV_TEST;
        $IS_CONSOLE = (php_sapi_name() == 'cli') ? true : false;
        if (!$PROJECT_DEBUG && !$overDebug) {
            return false;
        }
        if (!$IS_CONSOLE) {
            echo '<div class="debug">';
        } else {
            echo "::::DEBUG:::: ";
        }
        if ($showFrom) {
            $calledFrom = $calledFrom ?: debug_backtrace();
            if (!$IS_CONSOLE) {
                echo '<strong>';
            }
            echo $calledFrom[0]['file'];
            if (!$IS_CONSOLE) {
                echo '</strong>';
            }
            echo ' (line ';
            if (!$IS_CONSOLE) {
                echo '<strong>';
            }
            echo $calledFrom[0]['line'];
            if (!$IS_CONSOLE) {
                echo '</strong>';
            }
            echo ')';
        }
        if (!$IS_CONSOLE) {
            echo "\n<pre class=\"debug\">\n";
        } else {
            echo PHP_EOL;
        }

        $var = print_r($var, true);
        if ($showHtml) {
            $var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
        }
        echo $var;
        if (!$IS_CONSOLE) {
            echo "\n</pre></div>\n\n";
        } else {
            echo PHP_EOL;
        }
    }

    public static function GetEnvironment($key, $default = false)
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;
        }

        return $value;
    }

    public static function MbCutString($str, $length, $postfix = '...', $encoding = 'UTF-8')
    {
        if (mb_strlen($str, $encoding) <= $length) {
            return strip_tags($str);
        }

        $tmp = mb_substr($str, 0, $length, $encoding);

        return strip_tags(mb_substr($tmp, 0, mb_strripos($tmp, ' ', 0, $encoding), $encoding) . $postfix);
    }

    public static function checkKeyedData($data, array $keys)
    {
        $result = true;
        $array = (array)$data;
        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                $result = $key;
                break;
            }
        }

        return $result;
    }

    protected static $_debugData = [];

    public static function saveDebug($data, $key = null)
    {
        if (is_null($key)) {
            static::$_debugData[] = $data;
        } else {
            if (empty(static::$_debugData[$key])) {
                static::$_debugData[$key] = [];
            }
            static::$_debugData[$key][] = $data;
        }
    }

    public static function getDebug()
    {
        return static::$_debugData;
    }

    public static function prepareKeyedText($array, $keyWrap = '')
    {
        $message = '';
        foreach ($array as $key => $value) {
            $message .= $keyWrap . $key . $keyWrap . ': ' . $value . PHP_EOL;
        }

        return $message;
    }

    /**
     * @param $date1
     * @param $date2
     * @return int
     */
    public static function daysBetweenDates($date1, $date2)
    {
        return (int)(new DateTime($date2))->diff(new DateTime($date1))->format("%a");
    }

    public static function getOneDayBefore($date)
    {
        return \DateTime::createFromFormat('Y-m-d', $date)->modify("-1 day")->format('Y-m-d');
    }
}