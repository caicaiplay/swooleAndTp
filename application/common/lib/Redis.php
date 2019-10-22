<?php
/**
 * Created by PhpStorm.
 * User: nange
 * Date: 2019/10/14
 * Time: 15:05
 */

namespace app\common\lib;

class Redis
{
    // 储存redis的一个单例
    private static $_instance = null;

    // redis的链接资源
    private $link;

    /**
     * 初始化redis
     * Redis constructor.
     */
    private function __construct()
    {
        $this->link = new  \Redis();
        try {
            $this->link->connect(RedisConf::HOST,RedisConf::PORT, RedisConf::OVERTIME);
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * 私有化克隆
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * 单例一个对象
     * @return Redis|null
     */
    public static function getInstance()
    {
        if (self::$_instance  === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * 获取值
     * @param $key
     * @return bool|string
     */
    public function getVal($key)
    {
        if (!$key) {
            return '';
        }
        return $this->link->get($key);

    }

    /**
     * 设置值
     * @param $key
     * @param $val
     * @param int $expire
     * @return bool
     */
    public function setVal($key, $val, $expire = 0)
    {
        if (empty($key)) {
            return '';
        }
        if (is_array($val)) {
            $val = json_encode($val);
        }
        if ($expire == 0) {
            return   $this->link->setnx($key, $val);
        }

        return $this->link->setex($key,  $expire, $val);
    }

    public function __call($name, $arguments=[])
    {
        if (!$arguments[0] || is_array($arguments[0])) {
            return '';
        }

        if (empty($arguments) || count($arguments) > 3) {
            return '';
        }
        if (count($arguments) == 1) {
            return $this->link->$name($arguments[0]);
        } elseif (count($arguments) == 2) {
            if (is_array($arguments[1])) {
                $arguments[1] = json_encode($arguments[1]);
            }
            return $this->link->$name($arguments[0], $arguments[1]);
        } else {
            return $this->link->$name($arguments[0], $arguments[1], $arguments[2]);
        }
    }

    /**
     * 需要在单例切换的时候做清理工作
     */
    public function __destruct ()
    {
        self::$_instance->link->close();
        self::$_instance = NULL;
    }

}