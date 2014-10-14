<?php

/**
 * 本页仅供测试
 */

//---------------------------
//
//---------------------------
define('USER_LIMIT_ADMIN', 	0x1);
define('USER_LIMIT_NORMAL', 0x2);
define('USER_LIMIT_VIP', 	0x4);




class RegisterAction extends Action {

	// 全局错误参数
	//private $errors = null;

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }

	//-----------------------------
	// 检查参数函数  全局函数 $errors
	//-----------------------------
	protected function checkInput($name, $email, $pw, $pw2, $captcha)
	{
		
		// step 1: 有参数为空
		if(!($name && $email && $pw && $pw2 && $captcha))
		{
			return WARN_MUST_INPUT;
		}
			
		// step 2: 密码不一致
		if($pw !== $pw2)
		{
			return ERROR_PASSWD_DIFF;
		}
		
		// step 3: 检查长度，用户名>=20 && <32 
		if(strlen($name)>=21 || strlen($pw)>=32)
		{
			return ERROR_USERNAME_LENGTH;
		}
		
		// step 4: 检查是否有非法字符.
		if(0 === preg_match('/^[a-zA-Z0-9\x80-\xff]{4,20}$/i', $name))
		{
			return ERROR_USERNAME_INVALIDATE_CHAR;
		}
		
		// step 5: 检查是否全为数字.
		if(1 === preg_match('/^[0-9]{4,20}$/', $name))
		{
			return WARN_USERNAME_NUM;
		}
		
		// step 6: 检查验证码是否输入正确， 
		error_reporting(0);
		session_start();	//使用session.
		if($captcha !== intval($_SESSION['captcha']))
		{
			return ERROR_CAP_INPUT;
		}
		
		// step 7: 检查用户是否已经注册. (mysql 操作.)
		$User = M('bbs_users');
		$condition['name'] = $name;
		$db_user = $User->where($condition)->limit(1)->select('id');
		if($db_user)
		{
			return ERROR_USERNAME_EXIST;
		}
		
		return null;
	}

/*	
	#----------------------------------
	# 写注册信息到db中.
	#-----------------------------------
	protected function writeRegInfo($name, $email, $pw, $timestamp)
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
		$new_uid = $User->add($data);
		if(!$new_uid)
		{
			$this->error('数据写入错误！');
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
		$cur_skey = md5($new_uid.$pwmd5.$timestamp.'00');
		$data['id'] = $new_uid;
		set_online($data, $cur_skey, $timestamp);
		
		// step 4: 将已经登陆的信息设置到cookie中.
		
		setcookie("cur_uid", $new_uid, $timestamp+ 86400 * 365, '/');
		setcookie("cur_username", $name, $timestamp+86400 * 365, '/');
		setcookie("cur_skey", $cur_skey, $timestamp+86400 * 365, '/');
		setcookie("cur_limitflag", $limit_flag, $timestamp+86400 * 365, '/');
		header('location: /');
	}
*/
	/*
	 * 注册信息. (需要增加防恶意注册逻辑.)
	 */
    public function index() {
		 if ($this->isPost())
		{
			$name = addslashes(strtolower(trim($_POST["reg_user_name"])));
			$email = addslashes(trim($_POST["reg_user_email"]));
			$pw = addslashes(trim($_POST["reg_user_password"]));
			$pw2 = addslashes(trim($_POST["reg_user_password2"]));
			$captcha = intval(trim($_POST["reg_captcha"]));

			$errors = $this->checkInput($name, $email, $pw, $pw2, $captcha);
			if(!$errors)
			{
				writeRegInfo($name, $email, $pw, time(), DEFAULT_AVATAR, 1);
				//$this->error("注册成功");
			}
			else
			{
				$this->error($errors);
			}
        }
		
		// step 1: 是否有登陆态
		$user = is_login();
		if($user != null)
		{
			$is_login = 1;
			$user_name = $user['cur_username'];
			$this->assign('user_name', $user_name);
		}
		else
		{
			$is_login = 0;
		}
		
		$this->assign('is_login', $is_login);
		
        $this->display();
	}
}

?>
