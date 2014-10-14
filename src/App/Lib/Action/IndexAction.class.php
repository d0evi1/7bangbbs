<?php

include_once( 'App/Common/saetv2.ex.class.php' );

/**
 * 首页
 */

class IndexAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }

	/*
	 * 获取最近文章列表
	 */
	protected function getArticle($per_page_num){
		// 若cache存在，直接从cache中取.
		$MMC = getMemCache();
		$articledb = $MMC->get('home-article-list');
		
		// 若cache不存在，从db中加载.
		if(!$articledb)
		{
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
					ORDER BY edit_time DESC LIMIT ".$per_page_num;
			$sql = M();
			$t_article_list = $sql->query($query_sql);
				
			// 30s内有效
			$MMC->set('home-article-list', $t_article_list, 0, 30);
			$articledb = $MMC->get('home-article-list');
		}
		
		return $articledb;
	}
	
	
	/*
	 * 
	 */
    public function index() 
	{
		// step 1: 是否有登陆态
		$user = is_login();
		if($user)
		{
			$is_login = 1;
			$user_name = $user['cur_username'];
			$user_avatar = $user['cur_avatar'];
			$this->assign('user_name', $user_name);
			$this->assign('user_avatar', $user_avatar);
		}
		else
		{
			$is_login = 0;
		}
		
		$this->assign('is_login', $is_login);
		
		// step 2: 获取类别 （todo：通过memcache完成）
		$model=new Model();
		$categories = $model->table('bbs_categories')->order('seq')->select();
		$this->assign('t_categories', $categories);
	
		// step 3: 获取文章数 (todo: memcache)
		$total_msg_cnt = db_get_article_cnt();

		// step 4: 计算页显示东西.
		$settings = get_settings();
		$per_page_num = $settings['page_per_num'];
		$range_limit = $settings['page_range_limit'];
		$cur_page = 1;

		$total_page_num = get_total_page($total_msg_cnt, $per_page_num);
		$cur_page_head = get_current_page_head($total_page_num, $range_limit, $cur_page);
		$cur_page_range = get_current_page_range($total_page_num, $range_limit, $cur_page);
		$prev_page = get_prev_page($total_page_num, $cur_page);
		$next_page = get_next_page($total_page_num, $cur_page);
		
		$art_list = $this->getArticle($per_page_num);
		
		//exit('nnnn');
		$this->assign('cur_page_head', $cur_page_head);
		$this->assign('cur_page_range', $cur_page_range);
		$this->assign('prev_page', $prev_page);
		$this->assign('next_page', $next_page);
		$this->assign('t_articles', $art_list);
	
		// step 5: 新浪微博登陆页
		$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
		$code_url = $o->getAuthorizeURL(WB_CALLBACK_URL);
		$this->assign('code_url', $code_url);
	
        $this->display();
    }
}

?>
