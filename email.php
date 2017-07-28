<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/27 0027
 * Time: 14:56
 */
include_once 'vendor/autoload.php';

class swiftEmail
{
    protected $server;
    protected $port;
    protected $userName;
    protected $pass;
    protected $subject;
    protected $sender;
    protected $receiver;
    protected $body;
    protected $transport;
    protected $message;

    public function __construct($account,$info)
    {
        //账号信息
        $this->server = $account['server'];
        $this->port = $account['port'];
        $this->userName = $account['userName'];
        $this->pass = $account['pass'];

        //配置此次发送邮件的信息
        $this->subject = $info['subject'];
        $this->sender = $info['sender'];
        $this->receiver = $info['receiver'];
        $this->body = $info['body'];

    }

    public function initial()
    {
        $this->startTransport();
        $this->startMessage();
    }

    //配置信息
    public function startTransport()
    {
        $this->transport = new Swift_SmtpTransport($this->server, 25);
        $this->transport->setUsername($this->userName);
        $this->transport->setPassword($this->pass);
    }

    //配置消息内容
    public function startMessage()
    {
        $this->message = new Swift_Message($this->subject);
        $this->message->setFrom([$this->sender]);
        $this->message->setTo([$this->receiver]);
        $this->message->setBody($this->body);
    }

    //发送
    public function send()
    {
        $this->initial();

        $mailer = new Swift_Mailer($this->transport);
        try
        {
            $res = $mailer->send($this->message);
        }
        catch (Swift_SwiftException $e)
        {
            //echo 'sending email failed.The reason is: '.$e->getMessage();
        }

        if($res) return [1,'success'];
        else return[0,$e->getMessage()];
    }
}


