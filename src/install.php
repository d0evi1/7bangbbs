<?php

define('IN_SAESPOT', 1);
@header("content-Type: text/html; charset=UTF-8");

$sqlfile = dirname(__FILE__) . '/bbs_mysql.sql';
if(!is_readable($sqlfile)) 
{
	exit('���ݿ��ļ������ڻ��߶�ȡʧ��');
}

$fp = fopen($sqlfile, 'rb');
$sql = fread($fp, 2048000);
fclose($fp);

include (dirname(__FILE__) . '/config.php');
include (dirname(__FILE__) . '/include/mysql.class.php');

$DBM = new DB_MySQL;
$DBM->connect($servername_m, $dbport, $dbusername, $dbpassword, $dbname);
unset($servername_m, $dbusername, $dbpassword);

$DBM->select_db($dbname);
if($DBM->geterrdesc()) 
{
	if(mysql_get_server_info() > '4.1') 
	{
		$DBM->query("CREATE DATABASE $dbname DEFAULT CHARACTER SET $dbcharset");
	}
	else
	{
		$DBM->query("CREATE DATABASE $dbname");
	}
	
	if($DBM->geterrdesc()) 
	{
		exit('ָ�������ݿⲻ����, ϵͳҲ�޷��Զ�����, �޷���װ.<br />');
	} 
	else 
	{
		$DBM->select_db($dbname);
		//�ɹ�����ָ�����ݿ�
	}
}

$query - $DBM->query("SELECT COUNT(*) FROM bbs_settings", 'SILENT');
if(!$DBM->geterrdesc())
{
	// ��ջ���
	$MMC->flush();
	header('location: /');
	exit('�����Ѿ�װ���ˣ� �����ظ���װ�� ��Ҫ��װ����ɾ��mysql ��ȫ�����ݡ� <a href="/">����ֱ�ӽ�����ҳ</a><br />');
}

runquery($sql);
$DBM->close();

// ��ջ���
$MMC->flush();

// '<br /> ˳����װ��ɣ�<br /><a href="/">���������ҳ</a>';

function runquery($sql) 
{
	global $dbcharset, $DBM;

	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	foreach(explode(";\n", trim($sql)) as $query) 
	{
		$queries = explode("\n", trim($query));
		foreach($queries as $query) 
		{
			$ret[$num] .= $query[0] == '#' ? '' : $query;
		}
		$num++;
	}
	unset($sql);

	foreach($ret as $query) 
	{
		$query = trim($query);
		if($query) 
		{
			if(substr($query, 0, 12) == 'CREATE TABLE') 
			{
				$name = preg_replace("/CREATE TABLE ([a-z0-9_]+) .*/is", "\\1", $query);
				//echo '������ '.$name.' ... �ɹ�<br />';
				$DBM->query(createtable($query, $dbcharset));
			} 
			else
			{
				$DBM->query($query);
			}
		}
	}
}

function createtable($sql, $dbcharset) 
{
	$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
	$type = in_array($type, array('MYISAM', 'HEAP')) ? $type : 'MYISAM';
	return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).(mysql_get_server_info() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=$dbcharset" : " TYPE=$type");
}

header('location: /');

?>
