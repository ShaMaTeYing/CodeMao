<?php
// 本类由系统自动生成，仅供测试用途
class UserAction extends Action {
	function _initialize(){
		$is_black = M('black_user')->where(array('ip'=>get_client_ip(),'status'=>1))->count();
		if($is_black)
			$this->error('你已经被拉入黑名单，请联系管理员！');
		$userinfo=session('userinfo');
		$login=M('user')->where(array('id'=>$userinfo['id']))->find();
		$loginStatus=session('loginStatus');
		
	   //控制切换登录窗口
		$this->assign('loginStatus',session('loginStatus')?session('loginStatus'):0);
		if(session('loginStatus'))//登录成功则传值给模板变量
		{
			$this->assign('userinfoData',session('userinfo'));
		}
		
   	}
   	public function showDisablePage(){
   		$this->display();
   	}
	public function showLogin(){
		$this->display();
	}
	//登录判断函数
	public function checkLogin(){
		$username = $_POST['username'];//获取POST传值
		$psw =$_POST['password'];
		$user=M('user')->where(array('username'=>$username))->find();
		$data = array();
		//判断用户是否存在
		//$data['username']=$_POST['username'];
		//$data['realname']=$user['realname'];
		$data['username']=$user['realname'];
		if(!$user){
			$data['status'] = 1;
			$data['info'] = 'User does not exist!';
		}else{
			//判断用户是否禁用
			if($user['status']==0){
				$data['status'] = 2;
				$data['info'] = 'User is disabled!';
			}else{
				//判断用户的密码是否一致
				if($user['password']==$psw){
					$data['status'] = 3;
					$data['info'] = 'login successful!';
				}else {
					$data['status'] = 4;
					$data['info'] = 'wrong password!';
				}
			}
			
		}
		
		$this->ajaxReturn($data,'JSON');
	}
	public function login(){
		$login_msg_data = array();//记录登录信息
		$login_msg_data['ip']=get_client_ip();//获取ip
		import('ORG.Net.IpLocation');// 导入IpLocation类
		$Ip = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
		$location= $Ip->getlocation(get_client_ip()); // 获取某个IP地址所在的位置
		$login_msg_data['area'] =  $location['country'].$location['area'];
		$login_msg_data['login_time']=time();
		$login_msg_data['username']=$_POST['username'];
		$login_msg_data['password']=$_POST['password'];
		$username = $_POST['username'];//获取POST传值
		$psw =$this->myMD5($_POST['password']);
		
		
		$username=htmlspecialchars($username);//将HTML标签转义
		//$psw=htmlspecialchars($psw);
		$user=M('user')->where(array('username'=>$username))->find();
		$login_msg_data['user_id']=$user['id'];
		$login_msg=M('login_msg');
		//判断用户是否存在
		if(!$user){
			$login_msg_data['status']='用户不存在';
			$login_msg->add($login_msg_data);
			session('loginStatus',0);
			$this->error('用户不存在!');
		}
		//判断用户是否禁用
		if($user['status']==0){
			$login_msg_data['status']='用户已被禁用';
			session('loginStatus',0);
			$login_msg->add($login_msg_data);
			$this->error("你已经被禁用,请联系管理员解禁！",U('User/showDisablePage'));	
		}
		//判断用户的邮箱是否验证
		if($user['status']==2){
			$login_msg_data['status']='用户邮箱未验证';
			session('loginStatus',0);
			$login_msg->add($login_msg_data);
			$this->error('用户邮箱未验证!');
		}
		//判断用户的密码是否一致
		if($user['password']==$psw){
			$login_msg_data['status']='登录成功';
			session('loginStatus',1);//显示登录成功的界面
			session('userinfo',$user);//设置userinfo的值，以便传值给模板
			cookie('username',$username);
			cookie('password',$this->oneMD5($_POST['password']));
			//$this->success('登录成功',U('Index/index'));
			$ID=$login_msg->add($login_msg_data);
			if($ID){
				dump("ID");
			}else {
				$this->error('写入login_msg数据库失败!');
			}
			$this->redirect('Index/index');
		}else {
			$login_msg_data['status']='密码错误';
			session('loginStatus',0);//继续显示登录界面
			$login_msg->add($login_msg_data);
			$this->error('密码错误!');
		}
		
	}
	public function oneMD5($value){
			$value=md5($value);
		return $value;
	}
	public function myMD5($value){
		for($i=1;$i<=5;$i++){
			$value=md5($value);
		}
		return $value;
	}
	//退出登录函数
	public function logout(){
		session('loginStatus',null);
		$login_msg_data = array();
		$login_msg_data['ip']=get_client_ip();//获取ip
		import('ORG.Net.IpLocation');// 导入IpLocation类
		$Ip = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
		 
		$location= $Ip->getlocation(get_client_ip()); // 获取某个IP地址所在的位置
		$login_msg_data['area'] =  $location['country'].$location['area'];
		
		$login_msg_data['login_time']=time();
		$userinfo=session('userinfo');
		$login_msg_data['username']=$userinfo['username'];
		$login_msg_data['user_id']=$userinfo['id'];
		$login_msg_data['status']='退出成功';
		M('login_msg')->add($login_msg_data);
		//$this->success('退出成功！','index');
		session('userinfo',null);
		cookie('username',null);
		cookie('password',null);
		cookie(null);
		$this->redirect('User/showLogin');
	}
	/* 获取用户邮箱 */
	public function showGetMail(){
		$this->display();
	}

	/* 发送修改密码的验证邮件*/
	public function sendEmail(){
		$conditions['mail']=$_POST['email'];
		$userData=M('user')->where($conditions)->find();
		
		if($userData){
			//session('userinfo',$userData);
			$rd['username']="修改密码";
			$rd['passwrod']="修改密码";
			$rd['status']="1";
			$rd['school']="修改密码";
			$rd['motto']="修改密码";
			$rd['mail']=$_POST['email'];
			$rd['realname']="修改密码";
			$rd['major']="修改密码";
			$rd['nickname']="修改密码";
			$rd['register_time']=time();
			$rd['hash']=$this->getRandChar(40);
			$ipData=$this->getIpData();
			$rd['ip']=$ipData['ip'];
			$rd['area']=$ipData['area'];
			$registerId=M('register')->add($rd);
			$this->sendMail(1,$_POST['email'],$registerId);
			$this->success('发送成功！',U('Index/index'));
		}else{
			$this->error('此邮箱不存在，请重新输入！');
		}
	}
	/*修改密码*/
	public function showModifyPassword(){
		$this->display();
	}
	public function modifyPassword(){
		//dump($_POST);
		$oldPsw=$this->myMD5($_POST['oldPsw']);
		$newPsw=$this->myMD5($_POST['newPsw']);
		$userinfo=session('userinfo');
		if($oldPsw==$userinfo['password']){
			$saveData['password']=$newPsw;
			$userinfo['password']=$newPsw;
			M('user')->where(array('id'=>$userinfo['id']))->save($saveData);
			session('userinfo',$userinfo);
			$this->success("修改成功！",U("Index/index"));
		}
		else {
			$this->error("密码错误！请重新输入！",U("showModifyPassword"));
		}
	}
	/*存储修改后的密码*/
	public function savePassword(){
		if($_POST['password']==''){
			$this->error("密码不能为空！");
		}else if($_POST['repassword']==''){
			$this->error("确认密码不能为空！");
		}else if($_POST['repassword']!=$_POST['password']){
			$this->error("两次密码不一致！");
		}else {
			$userData=session('userinfo');
		
			$userData['password']=$_POST['password'];
			$count=M('user')->where('id='.$userData['id'])->save($userData);
			if($count)
			$this->success('修改成功！',U('Index/index'));
		}
	}

	/*
		生成随机字符串
	*/
	public function getRandChar($length){
	   $str = null;
	   $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
	   $max = strlen($strPol)-1;

	   for($i=0;$i<$length;$i++){
		$str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
	   }
	   return $str;
	}
    protected function sortArrByManyField(){
        $args = func_get_args();
        if(empty($args)){
            return null;
        }
        $arr = array_shift($args);
        if(!is_array($arr)){
            throw new Exception("第一个参数不为数组");
        }
        foreach($args as $key => $field){
            if(is_string($field)){
                $temp = array();
                foreach($arr as $index=> $val){
                    $temp[$index] = $val[$field];
                }
                $args[$key] = $temp;
            }
        }
        $args[] = &$arr;//引用值
        call_user_func_array('array_multisort',$args);
        return array_pop($args);
    }
    /*
     	排序思路，tips再开一个字段score记录用户对应题目的得分，判题时，第一次AC，根据tip信息计算出分数写入该字段
     	判题端暂时未写
     * */
	/*显示所有用户*/
	public function showAllUserRank(){
		if(!session('loginStatus'))//登录不成功则跳转
		{
			$this->redirect('User/showLogin');
		}
	
		$sortParam = $_POST['sort_param'];
		
		$where['nickname']  = array('like','%'.$sortParam.'%');
		$where['solve_problem']  = array('like','%'.$sortParam.'%');
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
		$map['status']  = 1;
		
		$User = M('User'); // 实例化User对象
		$list = $User->where($map)->select();
		for($i=0;$i<count($list);$i++){
			$list[$i]['score']=$list[$i]['library_score']+$list[$i]['ladder_score'];
		}
        $list=$this->sortArrByManyField($list,'score',SORT_DESC,'solve_problem',SORT_DESC,'submissions',SORT_ASC);
		for($i=0;$i<count($list);$i++){
			if($i>0&&$list[$i]['solve_problem']==$list[$i-1]['solve_problem']&&$list[$i]['submissions']==$list[$i-1]['submissions']){
				$list[$i]['rank']=$list[$i-1]['rank'];
			}else {
				$list[$i]['rank']=$Page->firstRow+$i+1;
			}
			if($list[$i]['rank']<=5){
				$list[$i]['color']='red';
			}else if($list[$i]['rank']<=10){
				$list[$i]['color']='orange';
			}else if($list[$i]['rank']<=15){
				$list[$i]['color']='purple';
			}else {
				$list[$i]['color']='blue';
			}
			$list[$i]['score']=$list[$i]['library_score']+$list[$i]['ladder_score'];
		}
		import('ORG.Util.Page');// 导入分页类
		$count      = count($list);// 查询满足要求的总记录数
		//dump($count);
		$Page       = new Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		$sublist = array_slice($list,$Page->firstRow,$Page->listRows);
		$this->assign('list',$sublist);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板
		
	}
	public function getAcArray(&$problemData,$user_problem,$user,$field){
		$conditions['user_id']=$user;
		$conditions['judge_status']='0';
		$problemData=M($user_problem)->distinct('problem_id')->field('problem_id')->where($conditions)->order('problem_id asc')->select();
		$ac=array();
		foreach($problemData as $k => $v){
			$ac[$problemData[$k]['problem_id']]=1;
		}
		return $ac;
	}
	public function getNotSolveArray($ac,$user_problem,$user,$field){
		$notSolve=M($user_problem)->where(array('user_id'=>$user))->distinct('problem_id')->field($field)->order('problem_id asc')->select();
		foreach( $notSolve as $k => $v){
			if(isset($ac[$notSolve[$k]['problem_id']])){
				unset($notSolve[$k]);
			}
		}
		foreach($notSolve as $i => $v){
			if($notSolve[$i]['problem_id']==$notSolve[$i-1]['problem_id']){
				unset($notSolve[$i]);
			}
		}
		return $notSolve;
	}
	public function showUserMessage(){
		$con['id']=$_GET['id'];
		$userInfo = M('user')
			->where($con)
			->find();
		$myRank = M('user')
			->where(array('solve_problem'=>array('gt',$userInfo['solve_problem'])))
			->count()+1;
		$problemData=array();
		$ac=$this->getAcArray($problemData,'user_problem',$_GET['id'],'problem_id');
		$notSolve=$this->getNotSolveArray($ac,'user_problem',$_GET['id'],'problem_id');
		$tipsData=M('tips')->where(array('user_id'=>$_GET['id']))->select();
		foreach($tipsData as $k => $v){
			$tipsData[$k]['problem_mark']=M('problem')->where(array('id'=>$tipsData[$k]['problem_id']))->find()['problem_mark'];
			
		}
		foreach($problemData as $k=>$v){
			$problemData[$k]['problem_mark']=M('problem')->where(array('id'=>$problemData[$k]['problem_id']))->find()['problem_mark'];
			
		}
		foreach($notSolve as $k=>$v){
			$notSolve[$k]['problem_mark']=M('problem')->where(array('id'=>$notSolve[$k]['problem_id']))->find()['problem_mark'];
			
		}
		//dump($notSolve);
		$this->assign('tipsData',$tipsData);
		$this->assign('notSolve',$notSolve);
		$this->assign('user',$userInfo);
		$this->assign('myRank',$myRank);
		$this->assign('problemData',$problemData);
		$this->assign('train_ac',$train_problemData);
		$this->assign('train_notSolve',$train_notSolve);
		$this->display();
	
	}
	public function showModifyUserMessage(){
		$userinfo=session('userinfo');
		$this->assign('userinfo',$userinfo);
		$this->display();
	}
	public function modifyUserMessage(){
		$userdata=session('userinfo');
		$psw=$this->myMD5($_POST['password']);
		if($userdata['password']!=$psw){
			$this->error("密码错误,请重新输入!");
		}
		foreach($_POST as $k => $v){
			$_POST[$k]=htmlspecialchars($_POST[$k]);
		}
		if($_POST['major']) $data['major']=$_POST['major'];
		if($_POST['school']) $data['school']=$_POST['school'];
		if($_POST['motto']) $data['motto']=$_POST['motto'];
		if($_POST['nickname']) {
			$data['nickname']=$_POST['nickname'];
			$userdata['nickname']=$data['nickname'];
			session('userinfo',$userdata);
		}
		$count=M('user')->where('id='.$userdata['id'])->filter('strip_tags')->save($data);
		if($count>0){
			
			if($data['nickname']){
				$newNameData['nickname']=$data['nickname'];
				$where['nickname']=M('user_problem')
									->where('user_id='.$userdata['id'])
									->find()['nickname'];
				M('user_problem')->where($where)->filter('strip_tags')->save($newNameData);
				
			}
			
		}
		$this->success('修改成功',U('Problem/showProblemList'));
	}
	public function showForgetPassword(){
		$registerValue=M('register')->where(array('id'=>$_GET['id']))->find();
		if($registerValue['hash']==$_GET['key']){
			$userData=M('user')->where(array('mail'=>$registerValue['mail']))->find();
			$this->assign('userData',$userData);
			$this->display();
		}else {
			$this->error("验证不通过！",U("Index/index"));
		}
	}
	public function forgetPassword(){
		$userData=M('user')->where(array('id'=>$_POST['userId']))->find();
		if($_POST['password']==''){
			$this->error('密码不能为空',U('showForgetPassword',array('userData',$userData)));
		}
		if($_POST['password']!=$_POST['repassword']){
			$this->error('两次密码不一致！',U('showForgetPassword',array('userData',$userData)));
		}else {
			$saveData['password']=$this->myMD5($_POST['password']);
			$ans=M('user')->where(array('id'=>$_POST['userId']))->save($saveData);
			if($ans){
				$this->success('修改密码成功！',U('User/showLogin'));
				
			}else {
				$this->error('修改密码失败！',U('Index/index'));
			}
		}
	}
	protected function getSubmitData($tableName,$class){
		$userinfo=session('userinfo');
		$tmp=M($tableName)->where(array('user_id'=>$userinfo['id']))->select();
		foreach($tmp as $k => $v){
			$tmp[$k]['class']=$class;
		}
		return $tmp;
	}
	protected function setLibraryScore($tmp){
		$problem=M('problem');
		foreach($tmp as $k => $v){
			if($tmp[$k]['judge_status']==0) {
				$tmp[$k]['score']=$problem->where(array('id'=>$tmp[$k]['problem_id']))->find()['difficulty'];
				$tmp[$k]['score']=2*$tmp[$k]['score']+5;
			}else $tmp[$k]['score']=0;
		}
		return $tmp;
	}
	protected function setLadderScore($tmp){
		$ladder_problem=M('ladder_contest_problem');
		$ladder_user_problem=M('ladder_judge_detail');
		$ladder_contest=M('ladder_contest');
		foreach($tmp as $k => $v){
			$tmp[$k]['title']=$ladder_contest->where(array('id'=>$tmp[$k]['contest_id']))->find()['name'];
			$ac=$ladder_user_problem->where(array('user_problem_id'=>$tmp[$k]['id'],'judge_status'=>0))->count();
			$sum=$ladder_user_problem->where(array('user_problem_id'=>$tmp[$k]['id']))->count();
			$problemScore=$ladder_problem->where(array('problem_id'=>$tmp[$k]['problem_id']))->find()['score'];
			$tmp[$k]['score']=$problemScore*$ac/$sum;
		}
		return $tmp;
	}
	protected function setTrain($tmp){
		$level_msg=M('level_msg');
		foreach($tmp as $k => $v){
			$tmp[$k]['title']=$level_msg->where(array('id'=>$tmp[$k]['level_msg_id']))->find()['level_title'];
		}
		return $tmp;
	}
	protected function setContest($tmp){
		$level_msg=M('contest_list');
		foreach($tmp as $k => $v){
			$tmp[$k]['title']=$level_msg->where(array('id'=>$tmp[$k]['contest_id']))->find()['name'];
		}
		return $tmp;
	}
	public function showAllSubmitRecord(){
		$librarySubmit=$this->getSubmitData('user_problem','library');
		$ladderSubmit=$this->getSubmitData('ladder_user_problem','ladder');
		$trainSubmit=$this->getSubmitData('train_user_problem','train');
		$contestSubmit=$this->getSubmitData('contest_user_problem','contest');
		$librarySubmit=$this->setLibraryScore($librarySubmit);
		$ladderSubmit=$this->setLadderScore($ladderSubmit);
		$trainSubmit=$this->setTrain($trainSubmit);
		$contestSubmit=$this->setContest($contestSubmit);
		
	}
	public function showRegisterPage(){
		$this->display();
	}
	public function addUser(){
		if($_POST['password']!=$_POST['repassword']){
			$this->error('密码不一致，请重新输入！',U('User/showRegisterPage'));
		}
		if(M('user')->where(array('username'=>$_POST['username']))->find()){
			$this->error('该用户名已经存在,请重新输入！',U('User/showRegisterPage'));
		}
		$_POST['password']=myMD5($_POST['password']);
		unset($_POST['repassword']);
		$_POST['status']=1;$_POST['root']=0;
		$_POST['accepted']=0;$_POST['submissions']=0;
		$_POST['solve_problem']=0;$_POST['Submitted_problem']=0;
		$_POST['register_time']=time();
		$res=M('user')->add($_POST);
		if($res) {
			$this->success("注册成功！",U('User/showLogin'));
		}else {
			$this->error("注册失败,请重新操作！",U('User/showRegisterPage'));
		}
		//dump($_POST);
	}
}