<?php 
if (!defined('IN_SAESPOT')) exit('error: 403 Access Denied'); 

echo '
<div class="title">
    <div class="float-left fs14">
        <a href="/">',$options['name'],'</a> &raquo; <a href="/n-',$c_obj['id'],'">',$c_obj['name'],'</a> (',$c_obj['articles'],')
    </div>';
	
	
if($cur_user && $cur_user['flag']>4){
    echo '<div class="float-right">
			<a href="/newpost/',$t_obj['cid'],'" rel="nofollow" class="newpostbtn">+发新帖</a>
		</div>';
}


echo '    <div class="c"></div>
</div>

<div class="main-box">
<div class="topic-title">
    <div class="topic-title-main float-left">
        <h1>',$t_obj['title'],'</h1>
        <div class="topic-title-date">
        By <a href="/member/',$t_obj['uid'],'">',$t_obj['author'],'</a> at ',$t_obj['addtime'],' • ',$t_obj['views'],'次点击';

/*
 * 收藏	
 */ 
if($t_obj['favorites']){
    echo ' • ',$t_obj['favorites'],'收藏';
}

/*
 * 文章的状态栏
 */
if($cur_user && $cur_user['flag']>4){
	// 回复
	if(!$t_obj['closecomment']){
        echo ' • <a href="#new-comment">回复</a>';
    }
	
	// 收藏
    if($in_favorites){
        echo ' • <a href="/favorites?act=del&id=',$t_obj['id'],'" title="点击取消收藏">取消收藏</a>';
    }else{
        echo ' • <a href="/favorites?act=add&id=',$t_obj['id'],'" title="点击收藏">收藏</a>';
    }
	
	// 管理员可编辑.
    if($cur_user['flag']>=99){
        echo ' &nbsp;&nbsp;&nbsp; • <a href="/admin-edit-post-',$t_obj['id'],'">编辑</a>';
    }
}

/*
 * 成员头像.
 */
echo '        </div>
    </div>
    <div class="detail-avatar"><a href="/member/',$t_obj['uid'],'">';
	
/*
 * 如何是爬虫.
 */ 
if($is_spider){
    echo '<img src="',$options['base_avatar_url'],'/',$t_obj['uavatar'],'.jpg" alt="',$t_obj['uauthor'],'" />';
}else{
    echo '<img src="/static/grey.gif" data-original="',$options['base_avatar_url'],'/',$t_obj['uavatar'],'.jpg" alt="',$t_obj['uauthor'],'" />';
}

/*
 * 文章内容部分的显示, 过滤防xxs漏洞.
 */
echo '    </a></div>
    <div class="c"></div>
</div>
<div class="topic-content">
',$options['ad_post_top'],'
<p>',htmlspecialchars_decode($t_obj['content']),'</p>
',$options['ad_post_bot'],'
</div>

</div>
<!-- post main content end -->';

/*
 * bshare分享. added by carrotli.
 */
echo '<div class="bshare-custom">
		<div class="bsPromo bsPromo1"></div>
		<a title="分享到新浪微博" class="bshare-sinaminiblog"></a>
		<a title="分享到腾讯微博" class="bshare-qqmb"></a>
		<a title="分享到QQ空间" class="bshare-qzone"></a>
		<a title="分享到人人网" class="bshare-renren"></a>
		<a title="分享到网易微博" class="bshare-neteasemb"></a>
		<a title="更多平台" class="bshare-more bshare-more-icon more-style-addthis"></a>
		<span class="BSHARE_COUNT bshare-share-count">0</span>
	 </div>
	 <script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/button.js#style=-1&amp;uuid=60c86b28-a14b-473a-a131-12a6d61006a9&amp;pophcol=2&amp;lang=zh"></script>
	 <script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/bshareC0.js"></script>';

/*
 * 评论部分.
 */ 
if($t_obj['comments']){
	echo '
	<div class="title">
		',$t_obj['comments'],' 回复  |  直到 ',$t_obj['edittime'],'
	</div>
	<div class="main-box home-box-list">';

	// 
	$count_n = ($page-1)*$options['commentlist_num'];
	
	foreach($commentdb as $comment){
	$count_n += 1;
	echo '
		<div class="commont-item">
			<div class="commont-avatar"><a href="/member/',$comment['uid'],'">';
			
	if($is_spider){
		echo '    <img src="',$options['base_avatar_url'],'/',$comment['avatar'],'.jpg!normal" alt="',$comment['author'],'" />';
	}else{
		//echo '    <img src="/static/grey.gif" data-original="',$options['base_avatar_url'],'/',$comment['avatar'],'.jpg!normal" alt="',$comment['author'],'" />';
		echo '    <img src="/static/grey.gif" data-original="',$options['base_avatar_url'],'/',$comment['avatar'],'.jpg" alt="',$comment['author'],'" />';
	}
	
	echo '</a></div>
        <div class="commont-data">
            <div class="commont-content">
            <p>',$comment['content'],'</p>
            </div>
            
            <div class="commont-data-date">
                <div class="float-left"><a href="/member/',$comment['uid'],'">',$comment['author'],'</a> at ',$comment['addtime'];
	if($cur_user && $cur_user['flag']>=99){
		echo ' &nbsp;&nbsp;&nbsp; • <a href="/admin-edit-comment-',$comment['id'],'">编辑</a>';
	}
    echo '</div>
                <div class="float-right">';
				
	if(!$t_obj['closecomment'] && $cur_user && $cur_user['flag']>4 && $cur_user['name'] != $comment['author']){
		echo '&laquo; <a href="#new-comment" onclick="replyto(\'',$comment['author'],'\');">回复</a>'; 
	}
	
	echo '                <span class="commonet-count">',$count_n,'</span></div>
					<div class="c"></div>
				</div>
				<div class="c"></div>
			</div>
			<div class="c"></div>
		</div>';
}

/*
 * 评论超出，进行分页显示.
 */
if($t_obj['comments'] > $options['commentlist_num']){ 
	echo '<div class="pagination">';
	if($page>1){
		echo '<a href="/t-',$tid,'-',$page-1,'" class="float-left">&laquo; 上一页</a>';
	}
	
	if($page<$taltol_page){
		echo '<a href="/t-',$tid,'-',$page+1,'" class="float-right">下一页 &raquo;</a>';
	}
	
	echo '<div class="c"></div>
	</div>';
}

/*
 *
 */
echo '
    
</div>
<!-- comment list end -->

<script type="text/javascript">
function replyto(somebd){
    var con = document.getElementById("id-content").value;
    document.getElementsByTagName(\'textarea\')[0].focus();
    document.getElementById("id-content").value = " @"+somebd+" " + con;
}
</script>

';

}else{
    echo '<div class="no-comment">目前尚无回复</div>';
}

if($t_obj['closecomment']){
    echo '<div class="no-comment">该帖评论已关闭</div>';
}else{

if($cur_user && $cur_user['flag']>4){
echo '

<a name="new-comment"></a>
<div class="title">
    <div class="float-left">添加一条新回复</div>
    <div class="float-right"><a href="#">↑ 回到顶部</a></div>
    <div class="c"></div>    
</div>
<div class="main-box">';
if($tip){
    echo '<p class="red">',$tip,'</p>';
}
echo '    <form action="',$_SERVER["REQUEST_URI"],'#new-comment" method="post">
<input type="hidden" name="formhash" value="',$formhash,'" />
    <p><textarea id="id-content" name="content" class="comment-text mll">',htmlspecialchars($c_content),'</textarea></p>';

if(!$options['close_upload']){
    include(dirname(__FILE__) . '/upload.php');
}

echo '
    <p>
    <div class="float-left"><input type="submit" value=" 提 交 " name="submit" class="textbtn" /></div>
    <div class="float-right fs12 grey">• 请尽量让自己的回复能够对别人有帮助，不欢迎灌水，发现无内容测试、灌水一律禁言！</div>
    <div class="c"></div> 
    </p>
    </form>
</div>
<!-- new comment end -->';

}else{
    echo '<div class="no-comment">请 <a href="/login" rel="nofollow">登录</a> 后发表评论</div>';
}


}

?>