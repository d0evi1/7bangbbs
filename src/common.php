<?php
/**
 *程序官方支持社区 http://7bangbbs.sinaapp.com/
 *欢迎交流！
 *7bangBBS是开源项目，可自由修改，但要保留Powered by 链接信息
 */
define('SAESPOT_VER', '1.0');
if (!defined('IN_SAESPOT')) exit('error: 403 Access Denied');




/*-----------------------------------------------------
 * 全局公共对象
 * step 1: 获得IP地址: $onlineip.
 *----------------------------------------------------*/
if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) 
{
    $onlineip = getenv('HTTP_CLIENT_IP');
}
elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) 
{
    $onlineip = getenv('HTTP_X_FORWARDED_FOR');
}
elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) 
{
    $onlineip = getenv('REMOTE_ADDR');
}
elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) 
{
    $onlineip = $_SERVER['REMOTE_ADDR'];
}

$onlineip = addslashes($onlineip);
//if(!$onlineip) exit('error: 400 no ip');

/*------------------------------------------------
 * step 2: 获取各全局性参数.
 *-----------------------------------------------*/
$mtime = explode(' ', microtime());
$starttime = $mtime[1] + $mtime[0];
$timestamp = time();
$php_self = addslashes(htmlspecialchars($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']));
$url_path = substr($php_self, 1,-4);


/*------------------------------------------------
 * step 3: 连接从数据库，读操作.
 *------------------------------------------------*/
include (dirname(__FILE__) . '/include/mysql.class.php');


//--------------------------------------------------
// 初始化从数据类，若要写、删除数据则需要定义主数据类
//--------------------------------------------------
$DBS = new DB_MySQL;
$DBS->connect($servername_s, $dbport, $dbusername, $dbpassword, $dbname);

/*
 * step 4: 关闭标志位。特殊字符提交的时候提示数据库错误
 * 所有的 ' (单引号), " (双引号), \ (反斜线) and 空字符会自动转为含有反斜线的转义字符。
 */
@set_magic_quotes_runtime(0);

// 判断 magic_quotes_gpc 状态
if (@get_magic_quotes_gpc()) {
    $_GET = stripslashes_array($_GET);
    $_POST = stripslashes_array($_POST);
    $_COOKIE = stripslashes_array($_COOKIE);
}

/*
 * step 5: 从cookie中获取当前用户
 */
$cur_user = null;
$cur_uid = $_COOKIE['cur_uid'];
$cur_uname = $_COOKIE['cur_uname'];
$cur_ucode = $_COOKIE['cur_ucode'];

// 清空缓存 测试时偶尔会用
//$MMC->flush();

/*
 * step 6: 判断当前用户是否超时.
 */
if($cur_uname && $cur_uid && $cur_ucode)
{
    $u_key = 'u_'.$cur_uid;
    // 若存在，尝试从缓存里取出
    $mc_user = $MMC->get($u_key);
    if($mc_user)
	{
        $mc_ucode = md5($mc_user['id'].$mc_user['password'].$mc_user['regtime'].$mc_user['lastposttime'].$mc_user['lastreplytime']);
        if($cur_uname == $mc_user['name'] && $cur_ucode == $mc_ucode)
		{
            $cur_user = $mc_user;
            unset($mc_user);
        }
    }
	else
	{
        // 若没有，从数据库里读取，由memcache缓存.
        $db_user = $DBS->fetch_one_array("SELECT * FROM bbs_users WHERE id='".$cur_uid."' LIMIT 1");
        if($db_user)
		{
            $db_ucode = md5($db_user['id'].$db_user['password'].$db_user['regtime'].$db_user['lastposttime'].$db_user['lastreplytime']);
            if($cur_uname == $db_user['name'] && $cur_ucode == $db_ucode)
			{
                //设置memcache缓存, 10分钟超时;  cookie: 一年.
                $MMC->set($u_key, $db_user, 0, 600);
                setcookie('cur_uid', $cur_uid, $timestamp+ 86400 * 365, '/');
                setcookie('cur_uname', $cur_uname, $timestamp+86400 * 365, '/');
                setcookie('cur_ucode', $cur_ucode, $timestamp+86400 * 365, '/');
                $cur_user = $db_user;
                unset($db_user);
            }
        }
    }
}

$formhash = formhash();

/*
 * 读取模型， 加载options.
 */
include (dirname(__FILE__) . '/model.php');

/*
 * 限制不能打开.php的网址
 */
if(strpos($_SERVER["REQUEST_URI"], '.php'))
{
    header('location: /404.html');
    exit('no php script');
}

/*
 * step 7: 只允许注册用户访问
 */
if($options['authorized'] && (!$cur_user || $cur_user['flag']<5))
{
    if( !in_array($url_path, array('login','logout','register','forgot')))
	{
        header('location: /login');
        exit('authorized only');
    }
}

/*
 * step 8: 网站暂时关闭
 */
if($options['close'] && (!$cur_user || $cur_user['flag']<99))
{
    if( !in_array($url_path, array('login','forgot')))
	{
        header('location: /login');
        exit('site close');
    }
}

/*
 * step 9: 检查浏览页面的访问者在用什么操作系统（包括版本号）浏览器（包括版本号）和用户个人偏好.
 * 设置相应模板编号（tp1）
 */
$user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
if($user_agent)
{
    $is_spider = preg_match('/(bot|crawl|spider|slurp|sohu-search|lycos|robozilla|google)/i', $user_agent);
    $is_mobie = preg_match('/(iPod|iPhone|Android|Opera Mini|BlackBerry|webOS|UCWEB|Blazer|PSP)/i', $user_agent);

    if($is_mobie)
	{
        // 设置模板前缀
        $viewat = $_COOKIE['vtpl'];
        if($viewat=='desktop')
		{
            $tpl = '';
        }
		else
		{
            $tpl = 'ios_';
        }
    }
	else
	{
        $tpl = '';
    }
}
else
{
    //exit('error: 400 no agent');
}

//step 10: 设置基本环境变量
/*
$cur_user
$is_spider
$is_mobie
$options
*/


//----------------------------------------
// step 4: 去除转义字符
//----------------------------------------
function stripslashes_array(&$array) 
{
	if (is_array($array)) 
	{
		foreach ($array as $k => $v) 
		{
			$array[$k] = stripslashes_array($v);
		}
	} 
	else if (is_string($array)) 
	{
		$array = stripslashes($array);
	}
	return $array;
}

//------------------------------------------
// 获得散列 (时间 +  用户id + 密码)
//------------------------------------------
function formhash() 
{
	global $cur_user, $timestamp;
	return substr(md5(substr($timestamp, 0, -7).$cur_user['id'].$cur_user['password']), 8, 8);
}

// 一些常用的函数
//-------------------------------- 
// 显示时间格式化
//--------------------------------
function showtime($db_time)
{
    $diftime = time() - $db_time;
    if($diftime < 31536000)
	{
        // 小于1年如下显示
        if($diftime>=86400)
		{
            return round($diftime/86400,1).'天前';
        }
		else if($diftime>=3600)
		{
            return round($diftime/3600,1).'小时前';
        }
		else if($diftime>=60)
		{
            return round($diftime/60,1).'分钟前';
        }
		else
		{
            return ($diftime+1).'秒钟前';
        }
    }
	else
	{
        // 大于一年
        return gmdate("Y-m-d H:i:s", $db_time);
    }
}

//------------------------------
// 格式化帖子、回复内容
//------------------------------
function set_content($text,$spider='0')
{
    global $options;
    // images
    $img_re = '/(http[s]?:\/\/?('.$options['safe_imgdomain'].').+\.(jpg|jpe|jpeg|gif|png))\w*/';
    if(preg_match($img_re, $text))
	{
        if(!$spider)
		{
            $text = preg_replace($img_re, '<img src="'.$options['base_url'].'/static/grey2.gif" data-original="\1" alt="" />', $text);
        }
		else
		{
            // 搜索引擎来这样显示 更利于SEO 参见 http://saepy.sinaapp.com/t/81
            $text = preg_replace($img_re, '<img src="\1" alt="" />', $text);
        }
    }
    // 各大网站的视频地址格式经常变，能识别一些，不能识别了再改。
    // youku
	if(strpos($text, 'player.youku.com'))
	{
	    $text = preg_replace('/http:\/\/player.youku.com\/player.php\/sid\/([a-zA-Z0-9\=]+)\/v.swf/', '<embed src="http://player.youku.com/player.php/sid/\1/v.swf" quality="high" width="590" height="492" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash"></embed>', $text);
	}
	
    if(strpos($text, 'v.youku.com'))
	{
        $text = preg_replace('/http:\/\/v.youku.com\/v_show\/id_([a-zA-Z0-9\=]+)(\/|.html?)?/', '<embed src="http://player.youku.com/player.php/sid/\1/v.swf" quality="high" width="590" height="492" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash"></embed>', $text);
    }
    // tudou
    if(strpos($text, 'www.tudou.com'))
	{
        if(strpos($text, 'programs/view'))
		{
            $text = preg_replace('/http:\/\/www.tudou.com\/(programs\/view|listplay)\/([a-zA-Z0-9\=\_\-]+)(\/|.html?)?/', '<embed src="http://www.tudou.com/v/\2/" quality="high" width="638" height="420" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash"></embed>', $text);
        }
		else
		{
            $text = preg_replace('/http:\/\/www.tudou.com\/(programs\/view|listplay)\/([a-zA-Z0-9\=\_\-]+)(\/|.html?)?/', '<embed src="http://www.tudou.com/l/\2/" quality="high" width="638" height="420" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash"></embed>', $text);
        }
    }
    // qq
    if(strpos($text, 'v.qq.com'))
	{
        if(strpos($text, 'vid='))
		{
            $text = preg_replace('/http:\/\/v.qq.com\/(.+)vid=([a-zA-Z0-9]{8,})/', '<embed src="http://static.video.qq.com/TPout.swf?vid=\2&auto=0" allowFullScreen="true" quality="high" width="590" height="492" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash"></embed>', $text);
        }
		else
		{
            $text = preg_replace('/http:\/\/v.qq.com\/(.+)\/([a-zA-Z0-9]{8,}).(html?)/', '<embed src="http://static.video.qq.com/TPout.swf?vid=\2&auto=0" allowFullScreen="true" quality="high" width="590" height="492" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash"></embed>', $text);
        }
    }
	
    // gist
    if(strpos($text, '://gist'))
	{
        $text = preg_replace('/(https?:\/\/gist.github.com\/[\d]+)/', '<script src="\1.js"></script>', $text);
    }
	
    // mentions
    if(strpos(' '.$text, '@'))
	{
        $text = preg_replace('/\B\@([a-zA-Z0-9\x80-\xff]{4,20})/', '@<a href="'.$options['base_url'].'/member/\1">\1</a>', $text);
    }
    
	// url
    if(strpos(' '.$text, 'http'))
	{
        $text = ' ' . $text;
        $text = preg_replace(
        	'`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i',
        	'$1<a href="$2" target="_blank" rel="nofollow">$2</a>',
        	$text
        );
        $text = substr($text, 1);
    }
    
    $text = str_replace("\r\n", '<br/>', $text);
    
    return $text;
}

//---------------------------------------
// 匹配文本里呼叫某人，为了保险，使用时常在其前后加空格，如 @admin 吧
//---------------------------------------
function find_mentions($text, $filter_name='')
{
    // 正则跟用户注册、登录保持一致
    preg_match_all('/\B\@([a-zA-Z0-9\x80-\xff]{4,20})/' ,$text, $out, PREG_PATTERN_ORDER);
    $new_arr = array_unique($out[1]);
    if($filter_name && in_array($filter_name, $new_arr))
	{
        foreach($new_arr as $k=>$v)
		{
            if($v == $filter_name)
			{
                unset($new_arr[$k]);
                break;
            }
        }
    }
	
    return $new_arr;
}

//------------------------
// 转换字符
//------------------------
function char_cv($string) {
	$string = htmlspecialchars(addslashes($string));
	return $string;
}

//------------------------
// 过滤掉一些非法字符
//------------------------
function filter_chr($string){
    $string = str_replace("<", "", $string);
    $string = str_replace(">", "", $string);
    return $string;
}

//------------------------
// 判断是否为邮件地址
//------------------------
function isemail($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

?>