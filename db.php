<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/27 0027
 * Time: 11:19
 */

class dbServer
{
    private $dbconfig=array(
        'type'=>'mysql',
        'host'=>'localhost',
        'port'=>'3306',
        'user'=>'',
        'pwd'=>'',
        'charset'=>'utf8',
        'dbname'=>'',
    );                                                                    //默认配置
    private $link;
    private static $instance;
    private $data;                                                                                            //需要操作的数据

    private function __construct($params=array())
    {
        $this->initAttr($params);
        $this->connectServer();
        $this->setCharset();
        $this->selectDefaultDb();
    }

    private function initAttr($params)                                                         //params是自定义的配置，initAttr方法的作用是将自定义的配置与默认配置合并形成配置文件
    {
        $this->dbconfig=array_merge($this->dbconfig,$params);
    }

    private function connectServer()                                                            //连接数据库
    {
        $type=$this->dbconfig['type'];
        $host=$this->dbconfig['host'];
        $port=$this->dbconfig['port'];
        $user=$this->dbconfig['user'];
        $pwd=$this->dbconfig['pwd'];
        $charset=$this->dbconfig['charset'];
        $dsn="$type:host=$host;port=$port;charset=$charset";
        if($link=new PDO($dsn,$user,$pwd))
        {
            $this->link=$link;
        }
        else
        {
            die('数据库连接失败,请与管理员联系');
        }
    }

    private function setCharset()                                                                  //设置编码不用单独调用因为已在构造函数中调用
    {
        $sql="set names {$this->dbconfig['charset']}";
        $this->query($sql);
    }

    private function selectDefaultDb()                                                        //设置数据库不用单独调用因为已在构造函数中调用
    {
        if($this->dbconfig['dbname']=='') return;
        $sql="use `{$this->dbconfig['dbname']}`";
        $this->query($sql);
    }

    public function insertQuery($sql,$batch=false)                                  //PDO预处理函数
    {
        $data= $batch? $this->data : array($this->data);
        $this->data=array();
        $sql='';
        $stmt=$this->link->prepare($sql);
        foreach ($data as $v)
        {
            if($stmt->execute($v)===false)
            {
                die('数据库操作失败，请与管理员联系');
            }
        }
        return $stmt;
    }

    public function createData($data)                                                         //将预处理需要的数据导入到对象中
    {
        $this->data=$data;
        return $this;
    }

    private function __clone(){}

    public function fetchRow($sql)                                                              //取一条记录
    {
        if($this->query($sql))
            return $this->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll($sql)                                                                 //取所有记录
    {
        if($this->query($sql))
            return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function query($sql)
    {
        return $this->link->query($sql);
    }

    public function execute($sql)
    {
        return $this->link->exec($sql);
    }

    public function getLastInsertId()
    {
        return $this->link->lastInsertId();
    }

    public static function getInstance($params=array())                          //实例化函数
    {
        if(!self::$instance instanceof self)
        {
            self::$instance=new self($params);
        }
        return self::$instance;
    }
}