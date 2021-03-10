<?php
namespace Zvinger\BaseClasses\app\modules\api\base\components;


use yii\filters\RateLimitInterface;

class IpRateLimiter implements RateLimitInterface
{
    /**
     * @var int
     */
    public $rateLimit;

    /**
     * @var int
     */
    public $timePeriod;
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    private $ip;

    public function __construct($ip, $rateLimit, $timePeriod, $key = 'rate-limit')
    {
        $this->ip = $ip;
        $this->rateLimit = $rateLimit;
        $this->timePeriod = $timePeriod;
        $this->key = $key;
    }


    /**
     * @return \yii\caching\CacheInterface
     */
    private function getCache()
    {
        return \Yii::$app->cache;
    }

    /**
     * Returns the maximum number of allowed requests and the window size.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @return array an array of two elements. The first element is the maximum number of allowed requests,
     * and the second element is the size of the window in seconds.
     */
    public function getRateLimit($request, $action)
    {
        return [$this->rateLimit, $this->timePeriod];
    }

    /**
     * Loads the number of allowed requests and the corresponding timestamp from a persistent storage.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @return array an array of two elements. The first element is the number of allowed requests,
     * and the second element is the corresponding UNIX timestamp.
     */
    public function loadAllowance($request, $action)
    {
        $cache = $this->getCache();

        return [
            $cache->get('user.ratelimit.ip.allowance.'.$this->key.'.'.$this->ip),
            $cache->get('user.ratelimit.ip.allowance_updated_at.'.$this->key.'.'.$this->ip),
        ];
    }

    /**
     * Saves the number of allowed requests and the corresponding timestamp to a persistent storage.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @param int $allowance the number of allowed requests remaining.
     * @param int $timestamp the current timestamp.
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $cache = $this->getCache();
        $cache->set('user.ratelimit.ip.allowance.'.$this->key.'.'.$this->ip, $allowance);
        $cache->set('user.ratelimit.ip.allowance_updated_at.'.$this->key.'.'.$this->ip, $timestamp);
    }
}
