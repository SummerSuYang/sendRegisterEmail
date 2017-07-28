<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/27 0027
 * Time: 17:14
 */
include_once 'email.php';
include_once 'redis.php';
include_once 'db.php';
date_default_timezone_set('PRC');
$dbPara = [
    'host' => '127.0.0.1',
    'user' => 'summer',
    'pwd' => 'summer',
    'dbname' => 'sendemail',
];

//连接数据库
$db = dbServer::getInstance($dbPara);

$redisPara = [
    'host' => '127.0.0.1',
    'port' => 6379,
    'auth' => 'redis'
];

//连接redis
$redis = new redisServer($redisPara);

//队列的名字
$queueName = 'listQueue';

$listLength = $redis->getListLength($queueName);

//有需要发送的邮件
if($listLength)
{
    $item = $redis->rpop($queueName);
    $arr = explode('%',$item);
    $password = file_get_contents('password.txt');

    $account = [
        'server' => 'smtp.163.com',
        'userName' => 'that_summers@163.com',
        'pass' => $password,
        'port' => 25,
    ];

    $body = '您于'.date('Y-m-d H:i:s',time()).'注册成功';
    $info = [
        'sender' => 'that_summers@163.com',
        'receiver' => $arr[1],
        'subject' => '恭喜您注册成功!',
        'body' => $body,
    ];

    $swiftMailer = new swiftEmail($account,$info);
    $res = $swiftMailer->send();

    //如果发送失败就把元素重新放回队列
    if($res[0]===0) $redis->rpush($queueName,$item);

    echo date('Y-m-d H:i:s',time())."  $res[1]  ".$info['receiver'].' 发件人： '.$info['sender'].PHP_EOL;
}



