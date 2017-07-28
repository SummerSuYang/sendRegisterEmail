<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/27 0027
 * Time: 10:43
 */
class redisServer
{
    private $host;
    private $port;
    private $auth='';
    public $redis;

    public function __construct($para)
    {
        $this->host = $para['host'];
        $this->port = $para['port'];
        if(isset($para['auth']))
            $this->auth = $para['auth'];

        //连接
        $this->connect();

        //返回
        return $this->redis;
    }

    public function connect()
    {
        $this->redis = new Redis();
        $this->redis->connect($this->host,$this->port) or die ('Connect failed');
        //如果密码不为空就需要验证
        if(isset($this->auth)) $this->redis->auth($this->auth) or die ('Authenticate failed');
    }

    //获取链表的长度
    public function getListLength($listName)
    {
        return $this->redis->lLen($listName);
    }

    //从左边插入链表
    public  function lpush($list,$value)
    {
        return $this->redis->lPush($list,$value);
    }

    //从链表的右边弹出元素
    public function rpop($list)
    {
        return $this->redis->rPop($list);
    }

    public function rpush($list,$value)
    {
        return $this->redis->rPush($list,$value);
    }
}