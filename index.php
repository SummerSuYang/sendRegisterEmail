<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/27 0027
 * Time: 10:20
 */
require_once 'redis.php';
require_once 'db.php';
echo 'ok';
date_default_timezone_set('PRC');
$NUM = 5;

$dbPara = [
    'host' => '127.0.0.1',
    'user' => 'summer',
    'pwd' => 'summer',
    'dbname' => 'sendemail',
];

$db = dbServer::getInstance($dbPara);

$redisPara = [
    'host' => '127.0.0.1',
    'port' => 6379,
    'auth' => 'redis'
];

//连接redis
$redis = new redisServer($redisPara);
//redis队列的名字
$queueName = 'listQueue';

echo $redis->getListLength($queueName) and die();

//用户数据
$name = mt_rand(1111,9999);
$email = 'suyang0513@126.com';
$passWord = sha1($name);
$date = date('Y-m-d H:i:s',time());

$sql = "insert into queues(name,email,created_at,password) values('$name','$email','$date','$passWord');";
$insertRes = $db->execute($sql);
if(!$insertRes) echo 'datebase error' and die();

//获取插入记录的id
$lastId = $db->getLastInsertId();

//插入队列
$value = $lastId.'%'.$email;
$pushRes = $redis->lpush($queueName,$value);

if($pushRes)
{
    echo 'please check your email and verify your account';
}

else
{
    echo 'register failed';
}



