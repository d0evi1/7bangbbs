<?php

/**
 * 新建文章.
 */


class AdminAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }
	
	/*
	 * 判断是否具有管理员资格.
	 */
	protected function isAdmin($user) {
		if(intval($user['cur_limit']) === 0x1)
		{
			return true;
		}
		
		return false;
	}
	
	/*
	 * 写一篇文章，到db.
	 */
	protected function newCategory($title, $content) {
		$User = M('bbs_articles');

		// cid:类别id   uid: 用户ip		
		$data["cid"] = 1;
		$data["uid"] = 9;
		$data["title"] = $title;
		$data["content"] = $content;
		$data["addtime"] = time();
		$data["edittime"] = time();
		$lastInsId = $User->add($data);
		if($lastInsId)
		{
			echo "插入数据 id 为：$lastInsId";
		}
		else 
		{
			$this->error('数据写入错误！');
		}
	}
	
	/*
	 * 跳转.
	 */
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
		
		// step 2: 验证管理员身份.
		if($this->isAdmin($user) === false)
		{
			exit("404 not found!");
		}
	
		// step 3: 验证类别.
		$t_cat = M('bbs_categories');
		$categories = $t_cat->order('id')->select();
		$this->assign('t_categories',$categories);
	
        $this->display();
    }
	
	/*
	 * 跳转.
	 */
    public function cache() {
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
		
		// step 2: 判断post请求.
		if($this->isGet())
		{
			$is_reset = addslashes(trim($_GET["_URL_"][2]));
			if($is_reset === "reset")
			{
				MemCache_Reset();
				header("location: /");
				exit;
			}
		}
	
        $this->display();
    }
	
	/*
	 * 显示文章.
	 */
	 public function category() {
		header("Content-Type:text/html; charset=utf-8");
		if ($this->isPost())
		{
			// 获取参数.
			$cate_action = addslashes(trim($_GET["_URL_"][2]));
			if($cate_action === "add")
			{
				// 判断url 为 add还是del.
				$cat_name = addslashes(strtolower(trim($_POST["input_new_cat"])));
				$t_cat = M('bbs_categories');
				$data['name'] = $cat_name;
				$t_cat->add($data);
			}
			else if($cate_action === "modify")
			{
				$cat_id = intval($_GET["_URL_"][3]);
				$name = addslashes((trim($_POST["input_cat_name".$cat_id])));
				$seq = addslashes((trim($_POST["input_seq".$cat_id])));

				$t_cat = M('bbs_categories');
				$condition['id'] = $cat_id;
				$data['name'] = $name;
				$data['seq'] = $seq;
				$result = $t_cat->where($condition)->data($data)->save();
				if($result !== false){
					$this->success('数据更新成功！');
				}else{
					$this->error('数据更新失败！');
				}
			}
			else if($cate_action === "del")
			{
				$cat_id = intval($_GET["_URL_"][3]);
				$name = addslashes((trim($_POST["input_cat_name".$cat_id])));
				$seq = addslashes((trim($_POST["input_seq".$cat_id])));

				$t_cat = M('bbs_categories');
				$condition['id'] = $cat_id;
				$condition['seq'] = $seq;
				$data['name'] = "未知类别";
				$result = $t_cat->where($condition)->data($data)->save();
				if($result !== false){
					$this->success('数据更新成功！');
				}else{
					$this->error('数据更新失败！');
				}
			}
			else
			{
				exit(404);
			}
		}
		
		header("location: /admin");
		exit;
    }
	
	
	/*
	 * 发表一篇文章.
	 */
	public function post() {
		// 检测已注册ip
		//global $MMC;
		//$regip = $MMC->get('regip_'.$onlineip);
	
        if ($this->isPost())
		{
			$title = addslashes(strtolower(trim($_POST["topic_title_input"])));
			$content = addslashes(trim($_POST["topic_content_input"]));

			echo "title=".$title;
			echo "content=".$content;
			
			$this->newArticle($title, $content);
			
			$this->error("发表成功");
        }
		else
		{
            $this->error('非法请求');
        }
    }
}

?>
