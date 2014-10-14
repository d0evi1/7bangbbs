<?php
/**
 * 官方社区 http://7bangbbs.sinaapp.com/
 * 7bangBBS是开源项目，可自由修改，但要保留Powered by 链接信息
 */

$IS_SAE = isset($_SERVER['HTTP_APPNAME']);
if($IS_SAE)
{
    //数据库主机名或IP 主
    $servername_m = SAE_MYSQL_HOST_M;
    //数据库主机名或IP 从
    $servername_s = SAE_MYSQL_HOST_S;
    //数据库用户名
    $dbusername = SAE_MYSQL_USER;
    //数据库密码
    $dbpassword = SAE_MYSQL_PASS;
    //数据库名
    $dbname = SAE_MYSQL_DB;
    //数据端口
    $dbport = SAE_MYSQL_PORT;
    
    //MySQL字符集
    $dbcharset = 'utf8';
    //系统默认字符集
    $charset = 'utf-8';
    
    // 定义缓存
    $MMC = memcache_init();
}
else
{
    //数据库主机名或IP 主
    $servername_m = 'localhost';
    //数据库主机名或IP 从
    $servername_s = 'localhost';
    //数据库用户名
    $dbusername = 'root';
    //数据库密码
    $dbpassword = '123';
    //数据库名
    $dbname = 'bbs';
    //数据端口
    $dbport = '3306';
    
    //MySQL字符集
    $dbcharset = 'utf8';
    //系统默认字符集
    $charset = 'utf-8';
    
    // 定义缓存
    $MMC = memcache_connect('127.0.0.1',11211);
}
?>