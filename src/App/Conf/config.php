<?php
//注意，请不要在这里配置SAE的数据库，配置你本地的数据库就可以了。

/**
 * 官方社区 http://7bangbbs.sinaapp.com/
 * 7bangBBS是开源项目，可自由修改，但要保留Powered by 链接信息
 */

$IS_SAE = isset($_SERVER['HTTP_APPNAME']);

if(IS_SAE)
{
	$MMC = memcache_init();
	return array(
		//'配置项'=>'配置值'
		'DB_TYPE' => 'mysql',
		'DB_HOST' => SAE_MYSQL_HOST_M.','.SAE_MYSQL_HOST_S,
		'DB_NAME' => SAE_MYSQL_DB,
		'DB_USER' => SAE_MYSQL_USER,
		'DB_PWD'  => SAE_MYSQL_PASS,
		'DB_PORT' => SAE_MYSQL_PORT,
		'DB_CHARSET' => 'utf8',
		'DB_PREFIX' => '', 
		
		'URL_CASE_INSENSITIVE'=>true,
		'SHOW_PAGE_TRACE'=>true,
		

		'URL_MODEL'=>1,
		'VAR_URL_PARAMS' => '_URL_', // PATHINFO URL参数变量
		'URL_HTML_SUFFIX'=>'.html'
	);
}
else
{
	//$MMC = memcache_init();
	//$MMC = memcache_connect('127.0.0.1',6379);
	$MMC = memcache_init();
	
	return array(
		//'配置项'=>'配置值'
		'DB_TYPE' => 'mysql',
		'DB_HOST' => 'localhost',
		'DB_NAME' => 'app_7bangbbs',
		'DB_USER' => 'root',
		'DB_PWD'  => 'zabazine',
		'DB_PORT' => 3306,
		'DB_CHARSET' => 'utf8',
		'DB_PREFIX' => '', 
		
		'URL_CASE_INSENSITIVE'=>true,
		'SHOW_PAGE_TRACE'=>true,
		

		'URL_MODEL'=>1,
		'VAR_URL_PARAMS' => '_URL_', // PATHINFO URL参数变量
		'URL_HTML_SUFFIX'=>'.html'
	);
}



?>