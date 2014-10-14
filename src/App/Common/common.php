<?php

//-----------------------------
// sae config.
//-----------------------------
define( "WB_AKEY" , '2790787615' );
define( "WB_SKEY" , 'a5730b21c1494fb508112255d5490fb6' );
define( "WB_CALLBACK_URL" , 'http://7bangbbs.sinaapp.com/login/weibo' );


//-------------------------
// 全局错误变量.
//-------------------------
define("ERROR_USERNAME_EXIST", '对不起，该名字已经被注册了，换另一个用户名试试吧！');
define("ERROR_CAP_INPUT", '验证码不正确');
define("ERROR_USERNAME_INVALIDATE_CHAR", '名字包含非法字符, 请检查');
define("ERROR_USERNAME_LENGTH", '用户名或密码太长了');
define("ERROR_PASSWD_DIFF", '密码、重复密码 输入不一致');
define("WARN_MUST_INPUT", '用户名、密码、重复密码、验证码 必填'); 
define("WARN_USERNAME_NUM", '名字不能全为数字');
define("ERROR_LOGIN", '您输入的用户名或密码错误，请重新输入！');
define("ERROR_WEIBO_LOGIN", '微博登陆失败!');

//------------------------------------
// 头像全局变量
//-----------------------------
define("DEFAULT_AVATAR", '/default.jpg');


//---------------------------------
// 登陆方式
//---------------------------------
define("REG_TYPE_MAIN", 1);
define("REG_TYPE_WEIBO", 2);
define("REG_TYPE_QQ", 3);

function show_db_errorxx(){
	exit('系统访问量大，请稍等添加数据');
}

$IS_SAE = isset($_SERVER['HTTP_APPNAME']);

if($IS_SAE)
{
	$MMC = memcache_init();
}
else
{
	$MMC = memcache_connect('127.0.0.1',11211);
}


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

/*------------------------------------------------
 * step 2: 获取各全局性参数.
 *-----------------------------------------------*/
$mtime = explode(' ', microtime());
$starttime = $mtime[1] + $mtime[0];
$timestamp = time();
$php_self = addslashes(htmlspecialchars($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']));
$url_path = substr($php_self, 1,-4);



/*
 * step 4: 关闭标志位。特殊字符提交的时候提示数据库错误
 * 所有的 ' (单引号), " (双引号), \ (反斜线) and 空字符会自动转为含有反斜线的转义字符。
 */
//set_magic_quotes_runtime(0);

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

// 判断 magic_quotes_gpc 状态
if (@get_magic_quotes_gpc()) {
    $_GET = stripslashes_array($_GET);
    $_POST = stripslashes_array($_POST);
    $_COOKIE = stripslashes_array($_COOKIE);
}

/*
 * 判断用户是否已经注册.
 */
function get_db_user($name)
{
	if(!$name)
	{
		return null;
	}
	
	$User = M('bbs_users');
	$condition['name'] = $name;
	$users = $User->where($condition)->limit(1)->select();
	$db_user = $users[0];
	if(!$db_user)
	{
		return null;
	}
	
	return $db_user;
}

/*
 *
 */
function getMemCache() 
{
	$IS_SAE = isset($_SERVER['HTTP_APPNAME']);

	//exit("fail:".$_SERVER['HTTP_APPNAME']);
	
	if($IS_SAE)
	{
		$MMC = memcache_init();
	}
	else
	{
		$MMC = memcache_connect('127.0.0.1',6379);
	}
	
	return $MMC;
}


/*
 * 设置在线.
 */
function set_online($db_user, $skey, $login_time)
{
	if(!($db_user && $skey && $login_time))
	{
		return -1;
	}

	// step 1: 检查是否已经在线.
	$u_key = 'u_'.$db_user['id'];

	// step 2: 设置在线状态表.
	$t_online = M("bbs_online");
	$data['id'] = $db_user['id'];
	$data['skey'] = $skey;
	$data['is_online'] = 1;
	$data['login_time'] = $login_time;
	$t_online->add($data);
	
	// step 3: 设置缓存登陆态, 2小时.
	$u_value = array(
		'user_id' 		=> $db_user['id'],
		'user_name' 	=> $db_user['name'],
		'user_skey' 	=> $skey,
		'user_limit' 	=> $db_user['limit_flag'],
		'user_avatar' 	=> $db_user['avatar']);
	$MMC = getMemCache();
	$MMC->set($u_key, $u_value, 0, 7200);
	
	// 3.设置cookie.
	ob_start();
	setcookie("cur_uid", $db_user['id'], $login_time+86400, '/');
	setcookie("cur_username", $db_user['name'], $login_time+86400 , '/');
	setcookie("cur_skey", $skey, $login_time+86400, '/');
	
/*	echo 'id'.$db_user['id'];
	echo 'name'.$db_user['name'];
	echo 'skey'.$skey;
	echo 'time:'.$login_time;
	echo "cookie".$_COOKIE['cur_uid'];
	echo $_COOKIE['cur_username'];
	ob_end_flush();
*/	
	return 0;
}

/*
 * 设置离线.
 */
function set_offline($user_id)
{
	// step 1: 清除表
	$t_online = M("bbs_online");
	$condition['id'] = $user_id;
	$t_online->where($condition)->delete();
	
	// step 2: 清除缓存
	$MMC = getMemCache();
	$MMC->delete('u_'.$user_id);
	
	// step 3: 清除cookie.
	$timestamp = time();
	setcookie("cur_uid", '', $timestamp-86400 * 365, '/');
	setcookie("cur_username", '', $timestamp-86400 * 365, '/');
	setcookie("cur_skey", '', $timestamp-86400 * 365, '/');
}


/*
 * 判断用户是否登陆.
 */
function is_login()
{
	// step 1: 从cookie中获取数据.
	$cur_user = null;
	$cur_uid = cookie('cur_uid');
	$cur_username = cookie('cur_username');
	$cur_skey = cookie('cur_skey');
	
	// step 2: 验证登陆态， 反解获取账号信息. 取出用户名.
	if(!($cur_username && $cur_uid && $cur_skey))
	{
		return null;
	}
	
	// step 3: 获取memcache.
	$u_key = 'u_'.$cur_uid;
	$MMC = getMemCache();
	$mc_user = $MMC->get($u_key);

	
	if($mc_user)
	{
		// 若存在，尝试从memcache缓存里, 取出当前用户名.
		if(($cur_skey !== $mc_user['user_skey']))
		{
			return null;
		}
		
		$cur_user['cur_uid'] = $mc_user['user_id'];
		$cur_user['cur_username'] = $mc_user['user_name'];
		$cur_user['cur_skey'] = $mc_user['user_skey'];
		$cur_user['cur_limit'] = $mc_user['user_limit'];
		$cur_user['cur_avatar'] = $mc_user['user_avatar'];
		//unset($mc_user);
	}
	else
	{
		// step 1: 若没有，尝试从数据库里读取. 再缓存数据. (只用来统计在线数据.)
		$t_login = M("bbs_online");
		$condition['id'] = $cur_uid; 
		$ret = $t_login->where($condition)->limit(1)->select();
		$db_login = $ret[0];	
		
		
		// step 2: 校验登陆表，最长4小时登陆时间.
		if((!$db_login)
			|| ($db_login['is_online'] !== 1)
			|| (time() - $db_login['login_time'] >= 14400)
			|| ($cur_skey !== $db_login['user_skey']))
		{
			// 不存在. 或 已经失效.
			return null;
		}
		
		// step 3: 取出资料，头像
		$t_user = M("bbs_users");
		$condition['id'] = $cur_uid;
		$ret = $t_user->where($condition)->limit(1)->select();
		$db_user = $ret[0];
		
		if(!$db_user)
		{
			return null;
		}
		
		$cur_avatar = addslashes($db_user['avatar']);
		
		
		// step 4: 重置memcache缓存, 10分钟超时;.
		$MMC->set($u_key, $db_login, 0, 7200);
		
		
		// step 5:   cookie: 1周
		setcookie('cur_uid', $cur_uid, $timestamp+86400 * 7, '/');
		setcookie('cur_username', $cur_username, $timestamp+86400 * 7, '/');
		setcookie('cur_skey', $cur_skey, $timestamp+86400 * 7, '/');
		setcookie('cur_avatar', $cur_avatar, $timestamp+86400 * 7, '/');
		
		$cur_user['cur_uid'] = $db_login['uid'];
		$cur_user['cur_username'] = $db_login['name'];
		$cur_user['cur_skey'] = $db_login['skey'];
		$cur_user['cur_limit'] = $db_login['limit_flag'];
		$cur_user['cur_avatar'] = $cur_avatar;
 		//unset($db_user);
	}
	
	return $cur_user;
}

/*
 * 是否是管理员.
 */
function is_admin($user)
{
	if(intval($user['cur_limit']) === 0x1)
	{
		return true;
	}
	
	return false;
}

/*
 * 用户权限.
 */
function is_user_right($user)
{
	if (!$user) {
		exit('error: 401 login please');
	}

	if ($user['flag']==0)
	{
		exit('error: 403 Access Denied');
	}
	else if($user['flag']==1)
	{
		exit('error: 401 Access Denied');
	}
	
	return 1;
}

/*
 * 用户是否存在.
 */
function is_user_exist($uid)
{
	// 
	$t_user = M('bbs_users');
	$condition['id'] = $uid;
	$ret = $t_user->where($condition)->limit(1)->select();
	if(!$ret)
	{
		return null;
	}
	
	return $ret[0];
}

//------------------------------------------
// 获得散列 (时间 +  用户id + 密码)
//------------------------------------------
function formhash() 
{
	global $cur_user, $timestamp;
	return substr(md5(substr($timestamp, 0, -7).$cur_user['id'].$cur_user['password']), 8, 8);
}

$formhash = formhash();

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
            return floor(round($diftime/86400,1)).'天前';
        }
		else if($diftime>=3600)
		{
			$hour = floor(round($diftime/3600,1));
            return $hour.'小时前';
        }
		else if($diftime>=60)
		{
			$min = floor(round($diftime/60,1));
            return $min.'分钟前';
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

/*
 * 获取共有多少页
 * @total_msg_cnt		总消息数
 * @per_page_num		每页显示消息数
 * 
 * @return			返回共多少页.
 */
function get_total_page($total_msg_cnt, $per_page_num)
{
	$page_num = intval(ceil($total_msg_cnt / $per_page_num));
	return $page_num;
}

/*
 * 获取当前页， 和当前的翻页标签
 * @total_msg_cnt		总消息数
 * @per_page_num		每页显示消息数
 * @range_limit			一个页签上显示的个数
 * @cur_page			当前页
 *
 * @return				返回当前属于哪个页签.
 */
function get_current_page_head($total_page_num, $range_limit, $cur_page)
{
	$range_page_head = 1 + intval(floor($cur_page / $range_limit) * $range_limit);
	return $range_page_head;
}

/*
 * 获取当前页， 和当前的翻页标签
 * @total_msg_cnt		总消息数
 * @per_page_num		每页显示消息数
 * @range_limit			一个页签上显示的个数
 * @cur_page			当前页
 *
 * @return				返回当前属于哪个页签.
 * 
 * 示例：
 * 1. $cur_page=11， $total_page_num=94页，如果range_limit=5, 那么最后结果展示为 5 = 10-15.
 * 2. $cur_page=93,  $total_page_num=94页，如果range_limit=5, 那么最后结果展示为 4 = 91-94
 */
function get_current_page_range($total_page_num, $range_limit, $cur_page)
{
	$range_page_head = intval(floor($cur_page / $range_limit) * $range_limit);
	$left_num = $total_page_num - $range_page_head;
	if($left_num >= $range_limit)
	{
		return $range_limit;
	}
	else if(($left_num < $range_limit) && ($left_num > 0) )
	{
		return $left_num;
	}
	
	return 0;
}

/*
 * 前一页
 */ 
function get_prev_page($total_page_num, $cur_page)
{
	$page = $cur_page - 1;
	if($page <= 0)
	{
		$page = 1;
	}
	
	return $page;
	
}

/*
 * 后一页
 */
function get_next_page($total_page_num, $cur_page)
{
	$page = $cur_page + 1;
	if($page >= $total_page_num)
	{
		$page = $total_page_num;
	}
	
	return $page;
}

/*
 * 获取设置选项
 */
function get_settings()
{
	$MMC = getMemCache();
	$mc_settings = $MMC->get('bbs_settings');
		
	if(!$mc_settings)
	{
		$query_sql = "SELECT * FROM bbs_settings";
		$sql = new Model;
		$db_settings = $sql->query($query_sql);

		$setting = array();
		
		foreach($db_settings as $db_setting)
		{
			$title = $db_setting['title'];
			$setting[$title] = $db_setting['value'];
		}
		
		$MMC->set('bbs_settings', $setting);
		$mc_settings = $MMC->get('bbs_settings');
	}
	
	return $mc_settings;
}

/*
 * db操作，获取文章数
 */
function db_get_article_cnt()
{
	// step 3: 文章列表
	$query_sql = "SELECT count(id) as cnt FROM bbs_articles";
	$sql = new Model;
	$ret = $sql->query($query_sql);
	if(!$ret)
	{
		return 0;
	}
	
	$total_msg_cnt = intval($ret[0]['cnt']);
	return $total_msg_cnt;
}


/*
 * 是否为最热的话题. (发表时间在4小时内.)
 */
function is_newest($create_time)
{
	$diftime = time() - $create_time;
	if($diftime < 60*60*6)
	{
		return 1;
	}
	
	return 0;
}

/*
 * 是否为最新主题. (4小时内)
 */
function is_hostest()
{
	
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

//-----------------------------
// 返回 length长度的随机数.
//-----------------------------
function rand_string($length)
{
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$rand_str = '';
	for ($i = 0; $i < $length; $i++)
	{
		$rand_str .= $chars[mt_rand(0, strlen($chars)-1)];
	}
	
	return $rand_str;
}



/*
 * 清空缓存
 */
function MemCache_Reset() 
{
	$IS_SAE = isset($_SERVER['HTTP_APPNAME']);

	if($IS_SAE)
	{
		$MMC = memcache_init();
		$MMC->flush();
	}
	else
	{
		$MMC = memcache_connect('127.0.0.1',11211);
		$MMC->flush();
	}
}

/*
 * 写入注册.
 */
function writeRegInfo($name, $email, $pw, $timestamp, $avatar = NULL, $reg_type=1, $weibo=NULL, $qq=NULL)
{
	// 权限位，是否是管理员.
	if($options['register_review'])
	{
		$limit_flag = 0x1;
	}
	else
	{
		$limit_flag = 0x2;
	}
	
	//----------------------------------------------------
	// step 1: 写数据到db中，二次加盐md5. 如果是第一个注册，成为管理员.
	//----------------------------------------------------
	$tmp_pwmd5 = md5($pw);
	$salt = rand_string(4);
	$final_pwmd5 = md5($tmp_pwmd5.$salt);
	$User = M('bbs_users');

	$data["name"] = $name;
	$data["limit_flag"] = $limit_flag;
	$data["email"] = $email;
	$data["passwd"] = $final_pwmd5;
	$data["reg_time"] = $timestamp;
	$data["salt"] = $salt;
	$data["avatar"] = $avatar;
	$data["reg_type"] = $reg_type;
	
	if($reg_type === 2)
	{
		$data["weibo_account"] = $weibo;
	}
	
	if($reg_type === 3)
	{
		$data["qq_acount"] = $qq;
	}
	
	$new_uid = $User->add($data);
	if(!$new_uid)
	{
		return -1;
	}
	else if($new_uid == 1)
	{
		$data["limit_flag"] = 0x1;
		$condition['id'] = 1;
		$User->where()->data($data)->save();
	}
	
	// step 2: 清除站点信息
	$MMC = getMemCache();
	$MMC->delete('site_infos');
	
	// step 3: 防恶意注册, 记录已注册ip
	$MMC->set('regip_'.$onlineip, '1', 0, intval($options['reg_ip_space']));
	
	// 2.设置online状态.
	$cur_skey = md5($new_uid.$final_pwmd5.$timestamp.'00');
	$data['id'] = $new_uid;
	set_online($data, $cur_skey, $timestamp);
	
	// step 4: 将已经登陆的信息设置到cookie中.
	ob_start();
	setcookie("cur_uid", $new_uid, $timestamp+ 86400, '/');
	setcookie("cur_username", $name, $timestamp+86400, '/');
	setcookie("cur_skey", $cur_skey, $timestamp+86400, '/');
	setcookie("cur_limitflag", $limit_flag, $timestamp+86400, '/');
	
	header('location: /');
	ob_end_flush();
	
}


?>