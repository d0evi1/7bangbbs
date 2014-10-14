<?php


/**
 * 登出
 */
class LogoutAction extends Action {

    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }
	
	/*
	 * 页面.
	 */
    public function index() {
		$cur_user = is_login();
		if($cur_user != null)
		{
			$is_login = 1;

			// step 1: 清除online表
			set_offline($cur_user['cur_uid']);
			header("location: /");
		}
		else
		{
			$is_login = 0;
			exit("404 not found");
		}
		
		$this->display();
	}
	
}

?>
