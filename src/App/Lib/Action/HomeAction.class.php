<?php

/**
 * 新建文章.
 */

define(ERR_UPLOAD_BIG, '上传头像：附件数据没有正确上传，或文件太大了');
define(ERR_UPLOAD_TYPE,	'您上传的图片不是jpg/png.');
define(ERR_UPLOAD_TYPE2, '您上传的图片内容不是jpg/png.');
define(ERR_UPLOAD_DB, '数据上传至服务器失败!');
define(ERR_UPLOAD_SAE, '数据上传至SAE失败!');


/*
 * 个人资料页.
 */
class HomeAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }
	
	/*--------------------------------------------------
	 * 上传头像.
	 *-------------------------------------------------*/
	protected function uploadImage($user) {
		
		// a.限制图片1M以内.
		if($_FILES['upload_avatar']['size'] && $_FILES['upload_avatar']['size'] >= 1048576)
		{
			return ERR_UPLOAD_BIG;
		}
		
		// b.获取上传的文件名、扩展名.
		$up_name = strtolower($_FILES['upload_avatar']['name']);
		$ext_name = pathinfo($up_name, PATHINFO_EXTENSION);
	
		// c. 限制扩展名.
		if(!in_array($ext_name, explode(",", 'jpg,png')))
		{
			return ERR_UPLOAD_TYPE;
		}
		
		// d.尝试以图片方式处理.
		$img_info = getimagesize($_FILES['upload_avatar']['tmp_name']);
		if(!$img_info)
		{
			return ERR_UPLOAD_TYPE2;
		}

		// e.是图片, 则创建源图片.
		if($img_info[2] == 2)
		{
			$img_obj = imagecreatefromjpeg($_FILES['upload_avatar']['tmp_name']);
			$t_ext = 'jpg';
		}
		else if($img_info[2] == 3)
		{
			$img_obj = imagecreatefrompng($_FILES['upload_avatar']['tmp_name']);
			$t_ext = 'png';
		}
		else
		{
			return ERR_UPLOAD_TYPE;
		}
		
		if(!isset($img_obj))
		{
			return ERR_UPLOAD_TYPE;
		}
		
		// f.是正确的图片格式
		$cur_uid = $user['cur_uid'];
		$new_name = substr(md5($cur_uid), 0, 12).'.'.$t_ext;
		$upload_dir = 'upload/'.$cur_uid;
		$upload_filename = $upload_dir.'/'.$new_name;
		
		// g.判断是不是动态gif
		$saeYun = new SaeStorage();
		$saeDomain = "avatar";

		// h.上传文件时， 先删除.
		//$saeYun->delete($saeDomain, $upload_filename);
		
		// i. 上传服务器.
		$imageUrl = $saeYun->upload($saeDomain, $upload_filename, $_FILES["upload_avatar"]["tmp_name"]);
		if(!$imageUrl)
		{
			return ERR_UPLOAD_SAE;
		}
		
		// g. 设置到数据库
		$url = $saeYun->getUrl($saeDomain, $upload_filename);
		$t_user = M("bbs_users");
		$data['avatar'] = $url;
		$condition['id'] = $cur_uid;
		$ret = $t_user->where($condition)->save($data);
		if($ret === false)
		{
			return ERR_UPLOAD_DB;
		}
			
		return;
	}
	
	/*--------------------------------
	 * 跳转.
	 *-------------------------------*/
    public function index() {
		// step 1: 判断用户是否登陆.
		$user = is_login();
		if($user != null)
		{
			$is_login = 1;
			$user_name = $user['cur_username'];
			$this->assign('user_name', $user_name);
			$this->assign('is_login', $is_login);
		}
		else
		{
			$is_login = 0;
			header("location: /login");
		}
		
		// step 3: 验证账号名是否存在.
		$uid = $user['cur_uid'];
		$member = is_user_exist($uid);
		if(!$member)
		{
			exit("404 not found!");
		}
		
		$this->assign('t_member', $member);
		
        $this->display();
    }
	
	/*-----------------------------------------
	 * 更改头像
	 *----------------------------------------*/
	public function updateAvatar() {
		// step 1: 判断用户是否登陆.
		$user = is_login();
		if($user != null)
		{
			$is_login = 1;
			$user_name = $user['cur_username'];
			$this->assign('user_name', $user_name);
			$this->assign('is_login', $is_login);
		}
		else
		{
			$is_login = 0;
			header("location: /login");
		}
		
		// step 2: 判断是否请求.
		if($this->isPost())
		{
			$ret = $this->uploadImage($user);
			if($ret)
			{
				$this->error("error:".$ret);
			}
			else
			{
				$this->success("上传成功.");
				header("location: /home");
			}
		}
	
		$this->display('index');
	}
	
	/*-------------------------------
	 * 查看个人简介.
	 *------------------------------*/
	public function member() {
		// step 1: 判断用户是否登陆.
		$user = is_login();
		if($user != null)
		{
			$is_login = 1;
			$user_name = $user['cur_username'];
			$this->assign('user_name', $user_name);
			$this->assign('is_login', $is_login);
		}
		else
		{
			$is_login = 0;
			header("location: /login");
		}
		
		// step 2: 获取账号名.
		$uid = intval(trim($_GET["_URL_"][2]));
		if(!$uid)
		{
			header("location: /home");
			exit;
		}
		
		// step 3: 验证账号名是否存在.
		$member = is_user_exist($uid);
		if(!$member)
		{
			exit("404 not found!");
		}
		
		$this->assign('t_member', $member);
		
		// step 4: 显示.
		$this->display();
	}
}

?>
