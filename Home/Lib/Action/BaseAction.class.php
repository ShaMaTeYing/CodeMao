<?php
// 本类由系统自动生成，仅供测试用途
class BaseAction extends Action {
   function _initialize(){
		$userinfo=session('userinfo');
		$userData=M('user')->where(array('id'=>$userinfo['id']))->find();
		if($userinfo&&$userData['status']!=1)
		{
			$this->error("你已经被禁用,请联系管理员解禁！",U('User/showDisablePage'));	
		}
		$loginStatus=session('loginStatus');
		
	   //控制切换登录窗口
		$this->assign('loginStatus',session('loginStatus')?session('loginStatus'):0);
		if(session('loginStatus'))//登录成功则传值给模板变量
		{
			$this->assign('userinfoData',session('userinfo'));
		}
		else 
		{
			$this->redirect('User/showLogin');
		}
   }
}