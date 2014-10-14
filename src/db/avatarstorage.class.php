<?php
/**
 * ͷ��洢��.
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
	 * �ϴ�ͷ��
	 */
	function upload($saeImage, $uploadImage)
	{
		$saeYun = new SaeStorage();
		return $this->saeYun->write($saeDomain, $saeImage, $uploadImage);
	}

	/*
	 * ͷ������
	 */
	function getUrl($saeImage)
	{
		return $this->saeYun->getUrl($saeDomain, $saeImage);
	}
}

?>
