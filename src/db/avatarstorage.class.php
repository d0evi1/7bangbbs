<?php
/**
 * 头像存储类.
 *
 * @version 1.0
 * @author jungle.
 * @copyright ? 2011, jungle. All rights reserved.
 */

class AvatarStorage{
	private $saeYun;
	private $saeDomain;

	public function version(){return '1.0.1';} 
	
	public function __construct()
	{
		$this->saeYun = new SaeStorage();
		$this->saeDomain = "avatar";
	}
	
	/*
	 * 上传头像
	 */
	function upload($saeImage, $uploadImage)
	{
		$saeYun = new SaeStorage();
		return $this->saeYun->write($saeDomain, $saeImage, $uploadImage);
	}

	/*
	 * 头像链接
	 */
	function getUrl($saeImage)
	{
		return $this->saeYun->getUrl($saeDomain, $saeImage);
	}
}

?>
