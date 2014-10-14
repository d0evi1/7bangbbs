<?php

/**
 * 新建文章.
 */


class CmsTopicAction extends Action {

	private $article_id;

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }
	
	
    public function index() {
        header("location: /topic/new_topic");
    }
	
	/*
	 * 显示文章.
	 */
	public function article() 
	{
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
	 
		// 获取文章 id.
		$cur_art_id = intval(trim($_GET["_URL_"][2]));
		cookie('article_id', $cur_art_id);
		
		if (!$cur_art_id)
		{
			exit("404 not found");
		}

		// step 1: 更新文章计数.
		$model = new Model();
		$model->execute("update cms_articles set views=views+1 where id=".$cur_art_id);
		
		// step 2: 获取文章信息
		$Articles = new Model();
		$art = $Articles->table('cms_articles articles, bbs_users users')
						->where("articles.id=".$cur_art_id." and articles.uid = users.id")
						->limit(1)
						->field("articles.*, users.name as user_name")
						->select();
		if(!$art)
		{
			exit("404 not found");
		}
		
		$article = $art[0];
		$this->assign('art', $article);
		
		// step 3: 获取对应的评论信息
		$comments = $model->table('cms_comments comments, bbs_users users')
				->where("comments.article_id =".$cur_art_id." and users.id = comments.uid")
				->field('comments.*, users.name as user_name')
				->select();
		
		$this->assign('t_comments', $comments);
		
		// step 4:  否是作者.
		if(intval($article['uid']) === intval($user['cur_uid']))
		{
			$this->assign('is_editable', 1);
		}
		else
		{
			$this->assign('is_editable', 0);
		}
		
		// step 5: 相关帖子
		$cur_cid = $article['cid'];
		$t_relative_articles = $model->query("select * from cms_articles where cid =".$cur_cid." and id!=".$cur_art_id." order by rand() limit 2");
		$this->assign('t_relative_articles', $t_relative_articles);
		
        $this->display();
    }
	
	/*
	 * 新建一篇文章.
	 */
	public function new_topic() 
	{
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
		if(is_admin($user) === false)
		{
			exit("404 not found!");
		}
		
		// step 2: 判断是否来自post请求, 如果是，则发表.
		if ($this->isPost())
		{
			$title = trim($_POST["input_topic_title"]);
			$content = trim($_POST["input_topic_content"]);
			$caterory = intval(trim($_POST["input_topic_category"]));
			$uid = $user['cur_uid'];
			
			$t_articles = M('cms_articles');

			// 写入数据库
			$data["cid"] = $caterory;
			$data["uid"] = $uid;
			$data["title"] = htmlspecialchars($title);
			$data["content"] = htmlspecialchars($content);
			$data["add_time"] = time();
			$data["edit_time"] = time();
			$lastInsId = $t_articles->add($data);
			if(!$lastInsId)
			{
				$this->error('数据写入错误！');
			}
			
			// 更新计数.
			$t_users = new Model();
			$t_users->execute("update bbs_users set articles=articles+1 where id=".$uid);
        }
		
		//  类别选取.
		$Categories = M('cms_categories');
		$cats = $Categories->select();
		$this->assign('t_cms_categories', $cats);
		
		$this->display();
	}
	
	
	/*
	 * 编辑一篇文章
	 */
	public function edit() 
	{
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
		
		// step 2: 判断当前用户是否是作者.
		$art_id = intval(trim($_GET["_URL_"][2]));
		if (!$art_id)
		{
			exit("404 not found");
		}
		
		if($this->isPost())
		{
			// 发表.
			
			// a. 更新文章.
			$title = trim($_POST["input_topic_title"]);
			$content = trim($_POST["input_topic_content"]);
			$caterory = intval(trim($_POST["input_topic_category"]));
			if(!($title && $content && $caterory))
			{
				exit("404 param empty.");
			}
			
			$t_articles = M('cms_articles');
			$condition['id'] = $art_id;
			$data['title'] = $title;
			$data['content'] = $content;
			$data['cid'] = $caterory;
			
			$ret = $t_articles->where($condition)->data($data)->save();
			if($ret)
			{
				header("location: /cms_topic/article/".$art_id);
			}
			else
			{
				$this->error("更新文章失败!");
			}
		}
		else
		{
			// 编辑.
			$t_articles = M('cms_articles');
			$condition['id'] = $art_id;
			$ret = $t_articles->where($condition)->limit(1)->select();
			if(!$ret)
			{
				exit("404 not found");
			}
			$article = $ret[0];
			
			if(intval($article['uid']) !== intval($user['cur_uid']))
			{
				exit("404 not found!!");
			}
			
			$this->assign("t_article", $article);
			
			//  类别选取.
			$Categories = M('cms_categories');
			$cats = $Categories->select();
			$this->assign('t_cms_categories', $cats);
			
			$this->display();
		}
	}
	
	/*
	 * 发表评论.
	 */
	public function comment() {
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
		
		$content = addslashes(trim($_POST["comment"]));
		$art_id = intval(trim(cookie('article_id')));

		// step 2: 添加评论.
		$Comment = M('bbs_comments');
		$data['article_id'] = $art_id;
		$data['uid'] = $user['cur_uid'];
		$data['add_time'] = time();
		$data['content'] = $content;
		$Comment->add($data);
		
		// step 3: 修改计数.
		$model = new Model;
		$model->execute("update bbs_articles set comments=comments+1 where id=".$art_id);
		
		// step 4: 修改用户的回复数.
		$t_users = new Model();
		$t_users->execute("update bbs_users set replies=replies+1 where id=".$uid);
		
		//$this->display('index');
		header("location: /");
	}
}

?>
