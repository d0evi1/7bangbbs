<?php

/**
 * 本页仅供测试
 */
class ShowAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }

	/*
	 * 获取文章.
	 */
	protected function getArticle($next_page, $per_page) {
		$query_sql = "SELECT a.id,
								a.cid,
								a.uid,
								a.reply_uid,
								a.title,
								a.add_time,
								a.edit_time,
								a.comments,
								c.name as cname,
								u.avatar as uavatar,
								u.name as author,
								ru.name as rauthor
					FROM bbs_articles a 
					LEFT JOIN bbs_categories c ON c.id=a.cid
					LEFT JOIN bbs_users u ON a.uid=u.id
					LEFT JOIN bbs_users ru ON a.reply_uid=ru.id
					ORDER BY edit_time DESC LIMIT ".$next_page.",".$per_page;
		$sql = new Model();
		$t_article_list = $sql->query($query_sql);
		return $t_article_list;
	}

	/*
	 * 首页：
	 */
    public function index() {
		exit("404 not found");
    }
	
	
	
	
	/*
	 * 时间线.
	 */
	public function timeline() {
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
	
		// step 1: 获取类别id.
		$cur_page_id = intval($_GET["_URL_"][2]);
		
		if ($cur_page_id<=0)
			$cur_page_id = 1;
		
		// step 1: 取得类别数据.
		$model=new Model();
		$categories = $model->table('bbs_categories')->order('seq')->select();
		$this->assign('t_categories', $categories);
		
		// step 2: 获取文章数据.	
		$settings = get_settings();
		$per_page_num = $settings['page_per_num'];
		$range_limit = $settings['page_range_limit'];
		$next_page = ($cur_page_id-1)*$per_page_num;
		
		$t_article_list = $this->getArticle($next_page, $per_page_num);
		
		// step 3: 
		$total_msg_cnt = db_get_article_cnt();
		$total_page_num = get_total_page($total_msg_cnt, $per_page_num);
		$cur_page_head = get_current_page_head($total_page_num, $range_limit, $cur_page_id);
		$cur_page_range = get_current_page_range($total_page_num, $range_limit, $cur_page_id);
		$prev_page = get_prev_page($total_page_num, $cur_page_id);
		$next_page = get_next_page($total_page_num, $cur_page_id);
		
		$this->assign('cur_page_id', $cur_page_id);
		$this->assign('cur_page_head', $cur_page_head);
		$this->assign('cur_page_range', $cur_page_range);
		$this->assign('prev_page', $prev_page);
		$this->assign('next_page', $next_page);
		
		$this->assign('t_articles',$t_article_list);
		
		$this->display();
	}

	/*
	 * 类别展示.
	 */
    public function category() {
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
	
	
		// 获取参数.
		$cate_id = intval(trim($_GET["_URL_"][2]));
		
		if ($cate_id == null)
			$cate_id = 1;
		
		// step 1: 取得类别数据.
		$model = new Model();
		$categories = $model->table('bbs_categories')->select();
		$this->assign('t_categories', $categories);
		
		// step 2: 获取文章数据.	
		$query_sql = "SELECT a.id,
							a.cid,
							a.uid,
							a.reply_uid,
							a.title,
							a.add_time,
							a.edit_time,
							a.comments,
							c.name as cname,
							u.avatar as uavatar,
							u.name as author,
							ru.name as rauthor
				FROM bbs_articles a 
				LEFT JOIN bbs_categories c ON c.id=a.cid
				LEFT JOIN bbs_users u ON a.uid=u.id
				LEFT JOIN bbs_users ru ON a.reply_uid=ru.id
				where a.cid = ".$cate_id." ORDER BY edit_time DESC LIMIT 50";
		$sql = M();
		$t_article_list = $sql->query($query_sql);
		
		$this->assign('category_id', $cate_id);
		$this->assign('t_articles',$t_article_list);
		
		$this->display('category');
	}
}

?>
