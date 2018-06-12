<?php
// 本类由系统自动生成，仅供测试用途
class LadderAdminAction extends BaseAction {
	function _initialize(){
		$userinfo=session('userinfo');
		$userData=M('user')->where(array('id'=>$userinfo['id']))->find();
		if($userinfo&&$userData['status']!=1)
		{
			$this->error("你已经被禁用,请联系管理员解禁！",U('User/showDisablePage'));	
		}
		$loginStatus=session('loginStatus');
		if($userinfo['root']<2){
			$this->error("非法访问！",U('Index/index'));
		}
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
	public function showContestListPage(){
		$contestListData=M('ladder_contest')->select();
		$this->assign('contestListData',$contestListData);
		$this->display();
	}
	public function showAddContestPage(){
		$this->display();
	}
	public function addContest(){
		$userinfo=session('userinfo');
		$contest=array('name'=>$_POST['name'],'join_number'=>0,
		'creat_time'=>time(),'is_visible'=>0,'creat_user_id'=>$userinfo['id']);
		$res=M('ladder_contest')->add($contest);
		if($res){
			$this->redirect('showContestListPage');
		}else{
			$this->error("添加失败！",U('showContestListPage'));
		}
	}
	public function showModifyContestPage(){
		$contestData=M('ladder_contest')->where(array('id'=>$_GET['id']))->find();
		$this->assign('contestData',$contestData);
		$this->display();
	}
	public function modifyContest(){
		$contest['name']=$_POST['name'];
		$res=M('ladder_contest')->where(array('id'=>$_POST['id']))->save($contest);
		if($res){
			$this->redirect('showContestListPage');
		}else{
			$this->error("添加失败！",U('showContestListPage'));
		}
	}
	public function changeStatus(){
		$contestData=M('ladder_contest')->where(array('id'=>$_GET['id']))->find();
		$modifyData['is_visible']=1-$contestData['is_visible'];
		M('ladder_contest')->where(array('id'=>$_GET['id']))->save($modifyData);
		$this->redirect('showContestListPage');
	}
	public function showProblemListPage(){
		if(!isset($_GET['id'])){
			$contestData=session('contestData');
			$_GET['id']=$contestData['id'];
		}
		$contestData=M('ladder_contest')->where(array('id'=>$_GET['id']))->find();
		session('contestData',$contestData);
		$problemModel=M('problem');
		$contestProblemData=M('ladder_contest_problem')->where(array('contest_id'=>$contestData['id']))->select();
		//dump($contestProblemData);
		foreach($contestProblemData as $k => $v){
			$contestProblemData[$k]['title']=$problemModel->where(array('id'=>$contestProblemData[$k]['problem_id']))->find()['title'];
		}
		$this->assign('contestProblemData',$contestProblemData);
		$this->display();
	}
	public function showAddProblemPage(){
		$contestData=session('contestData');
		$problemCnt=M('ladder_contest_problem')->where(array('contest_id'=>$contestData['id']))->count();
		$str="ABCDEFG";
		$problemMark=substr($str,$problemCnt,1);
		$this->assign('problemMark',$problemMark);
		$this->display();
	}
	public function addProblem(){
		$contestData=session('contestData');
		$contestProblemData['problem_id']=$_POST['problem_id'];
		$res=M('problem')->where(array('id'=>$contestProblemData['problem_id']))->find();
		if(!$res){
			$this->error("题目ID不存在！",U('showAddProblemPage'));
		}
		$contestProblemData['contest_id']=$contestData['id'];
		$contestProblemData['problem_mark']=$_POST['problem_mark'];
		$contestProblemData['is_visible']=0;
		$contestProblemData['score']=$_POST['score'];
		$res=M('ladder_contest_problem')->add($contestProblemData);
		if($res){
			$this->redirect('showProblemListPage');
		}else{
			$this->error("添加失败！",U('showProblemListPage'));
		}
	}
	public function showModifyProblemPage(){
		$contestData=session('contestData');
		$problemData=M('ladder_contest_problem')->where(array('id'=>$_GET['id']))->find();
		$this->assign('problemData',$problemData);
		$this->display();
	}
	public function modifyProblem(){
		$problem['problem_id']=$_POST['problem_id'];
		$problem['score']=$_POST['score'];
		$problem['problem_mark']=$_POST['problem_mark'];
		$res=M('ladder_contest_problem')->where(array('id'=>$_POST['id']))->save($problem);
		if($res){
			$this->redirect('showProblemListPage');
		}else{
			$this->error("添加失败！",U('showProblemListPage'));
		}
	}
	public function changeProblemStatus(){
		$contestData=M('ladder_contest_problem')->where(array('id'=>$_GET['id']))->find();
		$modifyData['is_visible']=1-$contestData['is_visible'];
		M('ladder_contest_problem')->where(array('id'=>$_GET['id']))->save($modifyData);
		$this->redirect('showProblemListPage');
	}
	public function showProblemPage(){
		$problemId=$_GET['id'];
		//echo "problemId".$problemId;
		$arr=M('problem')->find($problemId);
//		foreach($arr as $k => $v){
//			$arr[$k]=htmlspecialchars($arr[$k]);
//		}
		if(!$arr){
			$this->error('不要调皮，乱输入题号！',U("Index/index"));
		}
		$lastLan=session('lastLan');
		if(!$lastLan) $lastLan="C++";
		$lanData=array("C++","C","PASCAL");
		$this->assign('lanData',$lanData);
		$this->assign('lastLan',$lastLan);
		$this->assign('problemData',$arr);
		$this->display();
	}
}