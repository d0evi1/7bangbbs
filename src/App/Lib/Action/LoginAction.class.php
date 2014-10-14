<?php
ob_start();

include_once( 'config.php' );
include_once( 'saetv2.ex.class.php' );

/**
 * 登陆页面
 */
class LoginAction extends Action {

	protected $error_str = "";

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }

	/*
	 * 参数合法性校验.
	 */
	protected function checkInput($name, $pw, $captcha)
	{
		// step 1: 有参数为空
		if(!($name && $pw && $captcha))
		{
			$this->error_str = ERROR_LOGIN;
			return -1;
		}
		
		// step 2: 检查长度，用户名>=20 && <32 
		if(strlen($name)>=21 || strlen($pw)>=32)
		{
			$this->error_str = ERROR_LOGIN;
			return -2;
		}
		
		// step 3: 检查是否有非法字符.
		if(0 === preg_match('/^[a-zA-Z0-9\x80-\xff]{4,20}$/i', $name))
		{
			$this->error_str = ERROR_LOGIN;
			return -3;
		}
		
		// step 4: 检查是否全为数字.
		if(1 === preg_match('/^[0-9]{4,20}$/', $name))
		{
			$this->error_str = ERROR_LOGIN;
			return -4;
		}
		
		// step 5: 检查验证码是否输入正确， 
		error_reporting(0);
		session_start();	//使用session.
		if($captcha !== intval($_SESSION['captcha']))
		{
			$this->error_str = ERROR_CAP_INPUT;
			return -5;
		}
			
		return 0;
	}
	
	/*
	 * 设置在线.
	 */
	protected function setOnline($username, $pw) {
		
		// step 1: 判断用户是否已经注册.
		$db_user = get_db_user($username);
		if(!$db_user)
		{
			$this->error_str = ERROR_LOGIN;
			return -1;
		}
	
		// step 2: 检验密码.
		$input_pwmd5 = md5(md5($pw).$db_user['salt']);
		if($db_user['passwd']  !== $input_pwmd5)
		{
			$this->error_str = ERROR_LOGIN;
			return -2;
		}
	
		// step 3: 计算skey，设置在线状态.
		$login_time = time();
		$skey = md5($db_user['id'].$db_user['passwd'].$db_user['reg_time'].$login_time);
		$ret = set_online($db_user, $skey, $login_time);
		if($ret < 0)
		{
			$this->error_str = ERROR_LOGIN;
			return -3;
		}
	
		return 0;
	}
	
	/*
	 * 微博登陆
	 */
	public function weibo()
	{
		$o = new SaeTOAuthV2(WB_AKEY , WB_SKEY);

		if (isset($_REQUEST['code'])) {
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = WB_CALLBACK_URL;
			try {
				$token = $o->getAccessToken('code', $keys);
			} catch (OAuthException $e) {
				$this->error_str = ERROR_WEIBO_LOGIN;
				return -1;
			}
			
			if (!$token) {
				$this->error_str = ERROR_WEIBO_LOGIN;
				return -2;
			}
			// step 2: 设置session.
			$_SESSION['token'] = $token;
			setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
			
			$uid = $token['uid'];
			$access_token = $token['access_token'];

			// step 3: 获取用户信息.
			$client = new SaeTClientV2(WB_AKEY, WB_SKEY, $access_token);
			$weibo_info = $client->show_user_by_id($uid);			
			$wb_name = $weibo_info['screen_name'];
			$wb_avatar = $weibo_info['profile_image_url'];
			$wb_account = $weibo_info['profile_url'];

			// step 4:  是否存在该用户.
			$db_user = get_db_user($wb_name);
			if(!$db_user)
			{
				$ret = writeRegInfo($wb_name, 'weibo@sina.cn', md5($uid), time(), $wb_avatar, 2, $wb_account);
				if($ret < 0)
				{
					$this->error("writeRegInfo:".$ret);
					return -1;
				}
				
				$db_user = get_db_user($wb_name);
			}
			
			// step 6: 设置用户在线.
			$login_time = time();
			$ret = set_online($db_user, md5($access_token), $login_time);	
			if($ret < 0)
			{
				$this->error_str = ERROR_LOGIN;
				return -3;
			}
		}
		
		header("location:/");
		exit;
	}
	
	/*
	 * 页面.
	 */
    public function index() {
	
		if ($this->isPost())
		{			
			// 主站登陆方式.
			// step 1: 不带参数，直接显示登陆页
			$username = addslashes(trim($_POST["login_username"]));
			$password = addslashes(trim($_POST["login_password"]));
			$captcha = intval(trim($_POST["login_captcha"]));
			
			// step 2: 检查参数.
			$ret = $this->checkInput($username, $password, $captcha);
			if($ret != 0)
			{
				// a.验证失败，登陆失败
				$this->assign("error_str", $this->error_str);
				$this->display();
			}
			else
			{
				// b.验证成功，设置在线状态
				$ret = 	$this->setOnline($username, $password);
				if($ret < 0)	
				{
					$this->assign("error_str", $this->error_str);
					$this->display();
				}
			}
			
			header("location:/");
			exit;
		}
		
		$this->display();
		
	}
	
}

?>
