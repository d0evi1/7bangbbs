<?php 
if (!defined('IN_SAESPOT')) exit('error: 403 Access Denied'); 

// step 1: 最近更新.
echo '
<div class="title">
    <div class="float-left fs14">
        <a href="/">',$options['name'],'</a> &raquo; 最近更新 • <a href="/feed">Atom Feed</a>
    </div>';

// step 2: 如有用户，发新帖
if($cur_user && $cur_user['flag']>4 && $newest_nodes){
    echo '<div class="float-right"><a href="/newpost/1" rel="nofollow" class="newpostbtn">+发新帖</a></div>';
}

// step 3: 
echo '    <div class="c"></div>
</div>

<div class="main-box home-box-list">';


//--start-- step 4: 文章列表.
foreach($articledb as $article){
echo '
<div class="post-list">
    <div class="item-avatar"><a href="/member/',$article['uid'],'">';
    
if(!$is_spider){
    echo '<img src="',$options['base_avatar_url'],'/',$article['uavatar'],'.jpg" alt="',$article['author'],'" />';
}else{
    //echo '<img src="/static/grey.gif" data-original="',$options['base_avatar_url'],'/',$article['uavatar'],'.jpg!normal" alt="',$article['author'],'" />';
	echo '<img src="/static/grey.gif" data-original="',$options['base_avatar_url'],'/',$article['uavatar'],'.jpg" alt="',$article['author'],'" />';
}

echo '    </a></div>
    <div class="item-content">
        <h1><a href="/t-',$article['id'],'">',$article['title'],'</a></h1>
        <span class="item-date"><a href="/n-',$article['cid'],'">',$article['cname'],'</a>  •  <a href="/member/',$article['uid'],'">',$article['author'],'</a>';

if($article['comments']){
    echo ' •  ',$article['edittime'],' •  最后回复来自 <a href="/member/',$article['ruid'],'">',$article['rauthor'],'</a>';
}else{
    echo ' •  ',$article['addtime'];
}

echo '        </span>
    </div>';
    
if($article['comments']){
    $gotopage = ceil($article['comments']/$options['commentlist_num']);
    if($gotopage == 1){
        $c_page = '';
    }else{
        $c_page = '-'.$gotopage;
    }
    echo '<div class="item-count"><a href="/t-',$article['id'],$c_page,'#reply',$article['comments'],'">',$article['comments'],'</a></div>';
}

echo '    <div class="c"></div>
</div>';

}
//--end-- step 4: 文章列表.

if(count($articledb) == $options['home_shownum']){ 
echo '<div class="pagination">';
echo '<a href="/page/2" class="float-right">下一页 &raquo;</a>';
echo '<div class="c"></div>
</div>';
}

echo '</div>';

if(isset($bot_nodes)){
echo '
<div class="title">热门分类</div>
<div class="main-box main-box-node">
<span class="btn">';
foreach( $bot_nodes as $k=>$v ){
    echo '<a href="/',$k,'">',$v,'</a>';
}
echo '
</span>
<div class="c"></div>

</div>';
}

?>