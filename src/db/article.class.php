<?php
/**
 * ͷ��洢��.
 *
 * @version 1.0
 * @author jungle.
 * @copyright ? 2011, jungle. All rights reserved.
 */


class Article{
	private $title;
	private $content;
	
	public function __construct($title, $content)
	{
		$this->title = $title;
		$this->content = $content;
	}
	
	/*
	 * �ϴ�ͷ��, ʹ�õ���myisam��.
	 */
	public function write($db, $cid, $uin, $timestamp)
	{
		// ��������.
		$cur_title = $this->title;
		$cur_content = $this->content;
		$mysql_query = "INSERT INTO bbs_articles (id,cid,uid,title,content,addtime,edittime) 
						VALUES (null, $cid, $uin, '$cur_title', '$cur_content', $timestamp, $timestamp)";
		$db->query($mysql_query);
		$artitle_id = $db->insert_id();
		
		// ���¹����ļ���.
		$db->unbuffered_query("UPDATE bbs_categories 
							SET articles=articles+1 WHERE id='$cid'");
		$db->unbuffered_query("UPDATE bbs_users 
									SET articles=articles+1, 
									lastposttime=$timestamp 
								WHERE id='$uin'");
		return $artitle_id;
	}
	
}

?>
