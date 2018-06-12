<?php
// 本类由系统自动生成，仅供测试用途
class ForumAction extends BaseAction {
	public function index(){
		$userinfo=session('userinfo');
		if(!$userinfo){
			$this->error("请先登录!",U('User/showLogin'));
		}
		$this->display();
	}
	public function showForum(){
		$User = M('forum'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count      = $User->count();// 查询满足要求的总记录数
		$Page       = new Page($count,4);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$BBSData = $User->order('submit_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($BBSData as $key => $value){
			$BBSData[$key]['nickname']=M('user')->where(array('id'=>$BBSData[$key]['user_id']))->find()['nickname'];
		}
		//dump(U('showForum'));
		$this->assign("BBSData",$BBSData);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	public function saveForumData(){
		$levelId=session('levelId');
		$levelMsgId=session('levelMsgId');
		$userinfo=session('userinfo');
		$levelMsg=M('level_msg')->where(array('id'=>$levelId))->find();
		$this->assign("listData",$levelMsg);
		$BBSData['comment']=$_POST['html'];
		//$BBSData['level_msg_id']=$levelMsgId;
		$BBSData['user_id']=$userinfo['id'];
		$BBSData['submit_time']=time();
		M('forum')->add($BBSData);
		$resData['url']=U('showForum');
		$resData['status']=1;
		$this->ajaxReturn($resData, 'json');
	}
	
}