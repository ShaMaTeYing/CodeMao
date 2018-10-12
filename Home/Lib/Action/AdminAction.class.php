<?php
// 本类由系统自动生成，仅供测试用途
class AdminAction extends BaseAction {
	/* 构造函数 */
	function _initialize(){
		$userinfo=session('userinfo');
		if(!$userinfo||$userinfo['root']<1){
			saveViolationMessage('非法访问 后台'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
			$this->error("非法访问，请先登录!",U('User/showLogin'));
		}
		$login=M('user')->where(array('id'=>$userinfo['id']))->find();
		$loginStatus=session('loginStatus');
		$trainLevelData=M('level')->select();
		$this->assign('trainLevelData',$trainLevelData);
	   //控制切换登录窗口
		$this->assign('loginStatus',session('loginStatus')?session('loginStatus'):0);
		if(session('loginStatus'))//登录成功则传值给模板变量
		{
			$this->assign('userinfoData',session('userinfo'));
		}
	}
	/* 显示新增用户界面 */
	public function showAddUserPage(){
		$this->display();
	}
	/* 新增用户 */
	public function addUser(){
		if($_POST['password']!=$_POST['repassword']){
			$this->error('密码不一致，请重新输入！',U('Admin/showAddUserPage'));
		}
		if(M('user')->where(array('username'=>$_POST['username']))->find()){
			$this->error('该用户名已经存在,请重新输入！',U('Admin/showAddUserPage'));
		}
		$_POST['password']=myMD5($_POST['password']);
		unset($_POST['repassword']);
		$_POST['status']=0;$_POST['root']=0;
		$_POST['accepted']=0;$_POST['submissions']=0;
		$_POST['solve_problem']=0;$_POST['Submitted_problem']=0;
		$_POST['register_time']=time();
		$res=M('user')->add($_POST);
		if($res) {
			$this->success("新增成功！",U('Admin/showUserMessage'));
		}else {
			$this->error("新增失败,请重新操作！",U('Admin/showAddUserPage'));
		}
		//dump($_POST);
	}
	/*显示题库管理主界面*/
	public function showProblemLibrary(){
		$User = M('problem'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$condition['_logic'] = 'OR';
		$condition['title'] = array('like','%'.$_POST['id'].'%');
		$condition['id'] = array('like','%'.$_POST['id'].'%');
		$condition['description']=array('like','%'.$_POST['id'].'%');
		$count = $User->where($condition)->count();
		$Page  = new Page($count,100);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User->where($condition)->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $key => $value){
			$datapath='Data/Library/problems/'.$list[$key]['id'].'/1.in';
			//dump($datapath);
			if(file_exists($datapath)) $list[$key]['dataStatus']=1;
			else $list[$key]['dataStatus']=0;
		}
		$this->assign('problemData',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->assign("content",$_POST['id']);
		$this->display();
	} 
	/*显示添加题目主界面*/
	public function showAddProblem(){
		$this->display();
	}
	//删除空格
	public function trimall($str){
	    $qian=array(" ","　","\t","\n","\r");
	    $hou=array("","","","","");
	    return str_replace($qian,$hou,$str); 
	}
	/*添加题目*/
	public function creatProblemData(){
		$User = M("problem");
		foreach($_POST as $key=>$value){
			if($key!='stdexe')
			$_POST[$key]=htmlspecialchars($value);
		}
		$jsonData=array('label'=>$_POST['label'],
		'idea'=>$_POST['idea'],
		'stdexe'=>$_POST['stdexe']);
		$saveData=$_POST;
		$saveData['tips']=json_encode($jsonData);
		
		unset($saveData['label']);unset($saveData['idea']);unset($saveData['stdexe']);
		
		$problemId=M('problem')->max('id');
		$_POST['id']=$problemId+1;
		$count=$User->add($saveData);
		if($count) $this->success('success','showProblemLibrary');
		else $this->error('fail','showProblemLibrary');
	}
	
	/*删除题目*/
	public function deleteProblem(){
		$id=$_GET['id'];
		$User = M("problem"); // 实例化User对象
		$data['status']=$_GET['status'];
		$count=$User->where('id='.$id)->save($data); // 删除id为5的用户数据
		if($count>0) $this->success('success');
		else $this->error('fail');
	}
	/*显示上传页面*/
	public function showUpload(){
		//dump($_GET);
		$this->assign('problemdata',$_GET);
		$this->display();
	}
	/* 显示上传题目数据的页面 */
	public function showUpZipAddProblem(){
		$this->display();
	}
	public function findMatchString($str,$startStr,$endStr){
		$startPos=strpos($str,$startStr);
		$endPos=strpos($str,$endStr);
		$startStrLen=strlen($startStr);
		$endStrLen=strlen($endStr);
		$ansStrLen=$endPos-($startPos+$startStrLen);
		$ansStr=substr($str,$startPos+$startStrLen,$ansStrLen);
		$ansStr=trim($ansStr);
		if(strlen($ansStr)==0){
			$this->error($startStr.'字段不存在，请检查文本格式！',U('Admin/showUpZipAddProblem'));
		}
		return $ansStr;
	}
	public function getUpProblemData($txtData){
		$ans=array();
		$ans['title']=$this->findMatchString($txtData,"题目标题：","时间限制：");
		$ans['time_limit']=$this->findMatchString($txtData,"时间限制：","内存限制：");
		$ans['memory_limit']=$this->findMatchString($txtData,"内存限制：","难度：");
		$ans['submissions']=0;
		$ans['accepted']=0;
		$ans['description']=$this->findMatchString($txtData,"题目描述：","输入格式：");
		$ans['input']=$this->findMatchString($txtData,"输入格式：","输出格式：");
		$ans['output']=$this->findMatchString($txtData,"输出格式：","样例输入：");
		$ans['sample_input']=$this->findMatchString($txtData,"样例输入：","样例输出：");
		$ans['sample_output']=$this->findMatchString($txtData,"样例输出：","标签：");
		$ans['label']=$this->findMatchString($txtData,"标签：","作者：");
		$ans['author']=$this->findMatchString($txtData,"作者：","来源：");
		$source=substr($txtData,strpos($txtData,"来源：")+strlen("来源："));
		$source=trim($source);
		$ans['source']=$source;
		$ans['status']=1;
		$ans['output_limit']=0;
		$ans['case_number']=10;
		$ans['difficulty']=$this->findMatchString($txtData,"难度：","题目描述：");
		return $ans;
		
	}
	public function upZipAddProblem(){
		
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 3145728 ;// 设置附件上传大小
		//$upload->allowExts  = array('jpg', 'gif', 'png', 'txt');// 设置附件上传类型
		$upload->savePath =  'Data/Library/describe/';// 设置附件上传目录
		if(!$upload->upload()) {// 上传错误提示错误信息
		$this->error($upload->getErrorMsg());
		}else{// 上传成功 获取上传文件信息
		$info =  $upload->getUploadFileInfo();
		//dump($info);
		}
		$filepath=$info[0]['savepath'].$info[0]['savename'];
		$txtData=file_get_contents($filepath);
		$upProblemData=$this->getUpProblemData($txtData);
		foreach($upProblemData as $k => $v){
			$upProblemData[$k]=htmlspecialchars($upProblemData[$k]);
		}
		$User = M("problem");
		
		$labelString=$upProblemData['label'];
		$labelString=$this->trimall($labelString);
		$labelData=trim($labelData);
		$labelData=explode(";", $labelString);
		for($i=0;$i<count($labelData);$i++){
			$where['label_name']=$labelData[$i];
			$cnt=M('label_info')->where($where)->count();
			if($cnt==0){
				$data['label_name']=$labelData[$i];
				$data['status']=0;
				M('label_info')->data($data)->add();
			}
			$labelId=M('label_info')->where($where)->find();
			$problemId=$User->max('id');
			$problemLabelData['problem_id']=$problemId+1;
			$problemLabelData['label_id']=$labelId['id'];
			M('problem_label')->data($problemLabelData)->add();
		}
		unset($upProblemData['label']);
		//dump($_POST);
		//die;
		
		$upProblemData['id']=$problemId+1;
		$count=$User->add($upProblemData);
		if($count)
			$this->success('success','showProblemLibrary');
		else $this->error('fail','showProblemLibrary');
	}
	//扫描目录下的所有文件
	public function my_scandir($dir){  
	 $files=array();  
	 if(is_dir($dir)){  
	  if($handle=opendir($dir)){  
	   while(($file=readdir($handle))!==false){  
	    if($file!='.' && $file!=".."){  
	     if(is_dir($dir.$file)){  
	      $files[$file]=$this->my_scandir($dir.$file);  
	     }else{  
	      $files[]=$dir.$file;  
	     }  
	    }  
	   }  
	  }  
	 }  
	 closedir($handle);  
	 return $files;  
	}  
	
	public function fileIsOk($dir,$number){
		$files=$this->my_scandir($dir);
		for($i=1;$i<=$number;$i++){
			$in=$dir.$i.'.in';
			$out=$dir.$i.'.out';
			//dump($in);dump($out);
			if(!in_array($in,$files)) return 0;
			if(!in_array($out,$files)) return 0;
			//chmod($in,0777);
			//chmod($in,0777);
		}
		foreach($files as $key => $value){
			chmod($value,0777);
		}
		return 1;
		//die;
	}
	public function deleteOleFile($dir){
		$files=$this->my_scandir($dir);
		foreach($files as $key => $value){
			//dump("删除了 ".$value);
			unlink($value);
		}
	}
	/*上传文件*/
	public function upload(){
		
		$problem_id=$_POST['id'];
		$path='Data/Library/problems/'.$problem_id;
		if(!file_exists($path)) 
		{
			mkdir($path);
			chmod($path, 0777);
		}
		$this->deleteOleFile($path.'/');//删除目录下所有文件
		//设置上传参数
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 83886080 ;// 设置附件上传大小
		$upload->uploadReplace=true;
		$upload->savePath =  $path.'/';// 设置附件上传目录
		$upload->saveRule='';
		//$_FILES['input_path']['name'] = 'in';
		//$_FILES['output_path']['name'] = 'out';
		//$_FILES['file_path']['name'] = 'data.zip';
		
		if(!$upload->upload()) {// 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
		}else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
			$zip = new ZipArchive();//新建一个对象
			//dump($info);
			$filepath=$upload->savePath.$info[0]['name'];
			//dump($filepath);
			if ($zip->open($filepath) === TRUE)
			{
			    if(($zip->numFiles)%2==1){
			    	$this->error("数据文件格式错误！");
			    }
				$zip->extractTo($upload->savePath);//假设解压缩到在当前路径下images文件夹的子文件夹php
				if($this->fileIsOk($upload->savePath,$zip->numFiles))
					$this->error("数据文件格式错误！");
				
				$problemData=M('problem')
							->where(array('id'=>$problem_id))
							->find();
				$problemData['case_number']=($zip->numFiles)/2;
				M('problem')
						->where(array('id'=>$problem_id))
						->save($problemData);
				$zip->close();//关闭处理的zip文件
			}
			
			$this->success('success!','showProblemLibrary');
		}
	}
	/*显示修改题目界面*/
	public function showModifyProblem(){
		
		$id=$_GET['id'];
		$data=M('problem')->where('id='.$id)->find();
		
		$tipsData=json_decode($data['tips']);
		//dump($tipsData);
		$data['label']=$tipsData->label;
		$data['idea']=$tipsData->idea;
		$data['stdexe']=$tipsData->stdexe;
		$this->assign('data',$data);
		$this->display();
	}
	/*修改题目*/
	public function modifyProblemData(){
		//dump($_POST);
		
		foreach($_POST as $key=>$value){
			if($key!='stdexe')
			$_POST[$key]=htmlspecialchars($value);
		}
		$jsonData=array('label'=>$_POST['label'],
		'idea'=>$_POST['idea'],
		'stdexe'=>$_POST['stdexe']);
		$saveData=$_POST;
		$saveData['tips']=json_encode($jsonData);
		
		unset($saveData['label']);unset($saveData['idea']);unset($saveData['stdexe']);
		$data=$saveData;
		//设置该题目对应的标签不可用
		$count=M('problem')->where('id='.$_POST['id'])->save($data);
		if($count>0){
			$this->success('success!','showProblemLibrary');
		}else {
			$this->error('fail','showProblemLibrary');
		}
	}
	
	/*显示所有登录信息*/
	public function showLoginMessage(){
		$value=$_POST['value'];
		$where['username']  = array('like','%'.$value.'%');
		$where['area']  = array('like','%'.$value.'%');
		$where['ip']  = array('like','%'.$value.'%');
		//$where['login_time']  = array('like','%'.$value.'%');
		$where['status']  = array('like','%'.$value.'%');
		$where['_logic'] = 'or';
	

		$loginMessage=M('login_msg')->order('id desc')->where($where)->select();
		$this->assign('loginMessage',$loginMessage);
		
		$this->display();
	}
	public function showUserMessage(){
		$value=$_POST['value'];
		$where['username']  = array('like','%'.$value.'%');
		$where['nickname']  = array('like','%'.$value.'%');
		$where['realname']  = array('like','%'.$value.'%');
		//$where['login_time']  = array('like','%'.$value.'%');
		$where['mail']  = array('like','%'.$value.'%');
		$where['status']  = array('like','%'.$value.'%');
		$where['root']  = array('like','%'.$value.'%');
		$where['school']  = array('like','%'.$value.'%');
		$where['major']  = array('like','%'.$value.'%');
		$where['_logic'] = 'or';
		

		$map['_complex'] = $where;
		//$map['status']  = 1;
		$userinfo=session('userinfo');
		$map['root']=array('elt',$userinfo['root']);

		$userMessage=M('user')->where($map)->select();
		//dump($userMessage);
		$this->assign('userMessage',$userMessage);
		$this->display();
	}
	public function operation(){
		if($_GET['type']==1){
			if(M('user')->where('id='.$_GET['id'])->setField('status',$_GET['status']))
			$this->success('success!',U('Admin/showUserMessage'));
		}else {
			if(M('user')->where('id='.$_GET['id'])->setField('root',$_GET['status']))
			$this->success('success!',U('Admin/showUserMessage'));
		}
	}
	public function reJudge(){
		//dump($_GET);
		$data['judge_status']=8;
		$allJudgeRecord=M('user_problem')->where('problem_id='.$_GET['id'])->save($data);
		$this->success('success!',U('Admin/showProblemLibrary'));
	}
	public function showUserRecord(){
		//dump($_GET);
		//dump($id);
		$userinfo = session('userinfo');
		$problemId=$_POST['problemId'];
		
		$language=$_POST['language'];
		$judgeResults=$_POST['status'];
		if($_GET['id']) $where['user_id']=$_GET['id'];
		else $where['user_id']=$_POST['id'];
		if($language) $where['language']=$language;
		if($judgeResults) $where['judge_results']=$judgeResults;
		if($problemId) $where['problem_id']  = $problemId;
		
		//dump(M('user_problem')->where($where)->select());
		$where['_logic'] = 'and';
		//dump($where);
		$User = M('user_problem'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count = $User->where($where)->count();// 查询满足要求的总记录数
		$Page  = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$userinfo = session('userinfo');
		$this->myId = $userinfo['id'];
		$this->userId=$where['user_id'];
		$this->myRoot=$userinfo['root'];
		$this->display(); // 输出模板
	}
	public function userReJudge(){
		$data['judge_status']=8;
		$allJudgeRecord=M('user_problem')->where('id='.$_GET['id'])->save($data);
		//$this->showUserMessage();
		$this->success('success!',U('Admin/showUserRecord',array('id'=>$_GET['uid'])));
	}
	public function trainIndex(){
		
		$this->display();
	}
	public function showModifyLevel(){
		$levelData=M('level')->where(array("id"=>$_GET['id']))->find();
		$this->assign('levelData',$levelData);
		//dump($levelData);
		$this->display();
	}
	public function modifyLevel(){
		foreach($_POST as $key => $value){
			if($key!="id") $levelData[$key]=$value;
		}
		M('level')->where(array("id"=>$_POST['id']))->save($levelData);
		$this->redirect('showLevelList');
	}
	public function showLevelList(){
		$levelData=M('level')->select();
		$this->assign('levelData',$levelData);
		$this->display();
	}
	public function modifyTrainLevelStatus(){
		$statusData['status']=$_GET['status'];
		$res=M('level')->where(array("id"=>$_GET['id']))->save($statusData);
		if($res){
			$this->success('修改成功！',U('showLevelList'));
		}else {
			$this->error('修改失败！',U('showLevelList'));
		}
	}
	public function swapTask($data,$op){
		
		$now_priority=$data['priority'];
		$now_id=M('level_msg')->where(array('priority'=>$now_priority))->find()['id'];
		if($op=='lt'){
			$condition['priority']=array('lt',$now_priority);
			$pre_priority=M('level_msg')->where($condition)->max('priority');
		}
		else {
			$condition['priority']=array('gt',$now_priority);
			$pre_priority=M('level_msg')->where($condition)->min('priority');
		}
		$pre_id=M('level_msg')->where(array('priority'=>$pre_priority))->find()['id'];
		$nowData['priority']=$pre_priority;
		M('level_msg')->where(array('id'=>$now_id))->save($nowData);
		$preData['priority']=$now_priority;
		M('level_msg')->where(array('id'=>$pre_id))->save($preData);
	}
	public function showTaskList(){
		$minPriority=M('level_msg')->where(array("level_id"=>$_GET['id']))->min('priority');
		$maxPriority=M('level_msg')->where(array("level_id"=>$_GET['id']))->max('priority');
		$levelData=M('level')->where(array("id"=>$_GET['id']))->find();
		$this->assign('levelData',$levelData);
		if($_GET['op']==1){
			$this->swapTask($_GET,'lt');
		}
		else if($_GET['op']==2) $this->swapTask($_GET,'gt');
		$levelMsgData=M('level_msg')->where(array('level_id'=>$_GET['id']))->order('priority asc')->select();
		$train_problem=M('train_problem');
		foreach($levelMsgData as $k => $v){
			$levelMsgData[$k]['problem_number']=$train_problem->where(array('level_msg_id'=>$levelMsgData[$k]['id']))->count();
		}
		$this->assign('minPriority',$minPriority);
		$this->assign('maxPriority',$maxPriority);
		$this->assign('levelMsgData',$levelMsgData);
		$this->assign('levelMsgId',$_GET['id']);
		$this->display();
	}
	public function reJudgeTrain(){
		//dump($_GET);
		$data['judge_status']=8;
		$allJudgeRecord=M('train_user_problem')->where('problem_id='.$_GET['id'])->save($data);
		$this->success('success!',U('Admin/showTrainProblemList'));
	}
	public function showModifyTaskStatus(){
		$modifyStatus['status']=$_GET['status'];
		$res=M('level_msg')->where(array("id"=>$_GET['id']))->save($modifyStatus);
		if($res){
			$this->success("修改成功！",U('showTaskList',array('id'=>$_GET['levelId'])));
			
		}else {
			$this->error("修改失败！",U('showTaskList',array('id'=>$_GET['levelId'])));
		}
	}
	public function showModifyTask(){
		//dump($_GET);
		$levelMsgData=M('level_msg')->where(array("id"=>$_GET['id']))->find();
		//dump($levelMsgData);
		//dump($_GET);
		$this->assign('levelId',$_GET['levelId']);
		$this->assign('levelMsgData',$levelMsgData);
		$this->display();
	}
	public function modifyTask(){
		
		foreach($_POST as $key => $value){
			if($key!="id"&&$key!='levelId') $levelMsgData[$key]=$value;
		}
		M('level_msg')->where(array("id"=>$_POST['id']))->save($levelMsgData);
		$this->success("修改成功",U('showTaskList',array('id'=>$_POST['levelId'])));
	}
	public function showAddTask(){
		//dump($_GET);
		$this->assign('levelMsgId',$_GET['id']);
		$this->display();
	}
	public function addTask(){
		
		$priority=M('level_msg')->where(array("level_id"=>$_POST['level_id']))->max('priority');
		$priority=$priority+1;
		$levelMsgData['priority']=$priority;
		foreach($_POST as $key => $value){
			if($key!="id") $levelMsgData[$key]=$value;
		}
		M('level_msg')->add($levelMsgData);
		$this->success("新增成功",U('showTaskList',array('id'=>$_POST['level_id'])));
	}
	public function showTrainProblemList(){
		if($_GET['id']){
			session('trainLevelMsgId',$_GET['id']);
		}else {
			$_GET['id']=session('trainLevelMsgId');
		}
		
		$levelMsgData=M('level_msg')->where(array("id"=>$_GET['id']))->find();
		$trainProblemData=M('train_problem')->where(array("level_msg_id"=>$_GET['id']))->select();
		foreach($trainProblemData as $key => $value){
			$datapath='Data/Train/problems/'.$trainProblemData[$key]['id'].'/1.in';
			//dump($datapath);
			if(file_exists($datapath)) $trainProblemData[$key]['dataStatus']=1;
			else $trainProblemData[$key]['dataStatus']=0;
		}
		//dump($trainProblemData);
		$this->assign('levelMsgData',$levelMsgData);
		$this->assign('trainProblemData',$trainProblemData);
		$this->display();
	}
	public function modifyTrainProblemStatus(){
		
		$modifyStatus['status']=$_GET['status'];
		$res=M('train_problem')->where(array("id"=>$_GET['id']))->save($modifyStatus);
		if($res){
			$this->success("修改成功！",U('trainIndex'));
			
		}else {
			$this->error("修改失败！",U('trainIndex'));
		}
	}
	public function showTrainProblem(){
		$trainProblemData=M('train_problem')->where(array("id"=>$_GET['id']))->find();
		foreach($trainProblemData as $k => $v){
			$trainProblemData[$k]=htmlspecialchars($trainProblemData[$k]);
		}
		$this->assign('problemData',$trainProblemData);
		$this->display();
	}
	public function showTrainModifyProblem(){
		$trainProblemData=M('train_problem')->where(array("id"=>$_GET['id']))->find();
		$this->assign('data',$trainProblemData);
		$this->display();
	}
	public function modifyTrainProblemData(){
		$res=M('train_problem')->where(array("id"=>$_POST['id']))->save($_POST);
		$problem_mark=$_POST['problem_mark'];
		M('train_user_problem')->where(array("problem_id"=>$_POST['id']))->save($problem_mark);
		//dump($_POST);
		//die;
		if($res){
			$this->success("修改成功！",U('showTrainProblemList'));
		}else {
			$this->error("修改失败",U('showTrainProblemList'));
		}
	}
	public function showTrainAddProblem(){
		//$levelMsgData=M('level_msg')->select();
		$leveMsgId=$_GET['id'];
		$str="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$number=M('train_problem')->where(array('level_msg_id'=>$leveMsgId))->count();
		$problem_mark=substr($str,$number,1);
		$this->assign('problem_mark',$problem_mark);
		$this->assign('leveMsgId',$leveMsgId);
		//$this->assign('levelMsgData',$levelMsgData);
		$this->display();
	}
	public function creatTrainProblemData(){
		//dump($_POST);
		//die;
		$res=M('train_problem')->add($_POST);
		if($res>0){
			$this->success('新增成功！',U('showTrainProblemList'),array('id'=>$_POST['level_msg_id']));
		}else {
			$this->success('新增失败！',U('showTrainProblemList'),array('id'=>$_POST['level_msg_id']));
		}
	}
	public function showTrainUpFileAddProblem(){
		$this->assign('levelMsgId',$_GET['id']);
		$this->display();
	}
	public function trainUpFileAddProblem(){
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 3145728 ;// 设置附件上传大小
		//$upload->allowExts  = array('jpg', 'gif', 'png', 'txt');// 设置附件上传类型
		$upload->savePath =  'Data/Train/describe/';// 设置附件上传目录
		if(!$upload->upload()) {// 上传错误提示错误信息
		$this->error($upload->getErrorMsg());
		}else{// 上传成功 获取上传文件信息
		$info =  $upload->getUploadFileInfo();
		//dump($info);
		}
		$filepath=$info[0]['savepath'].$info[0]['savename'];
		$txtData=file_get_contents($filepath);
		$upProblemData=$this->getUpProblemData($txtData);
		$upProblemData['level_msg_id']=$_POST['id'];
		$str="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$number=M('train_problem')->where(array('level_msg_id'=>$_POST['id']))->count();
		$problem_mark=substr($str,$number,1);
		$upProblemData['problem_mark']=$problem_mark;
		$User = M("train_problem");
		
		
		unset($upProblemData['label']);
		
		$count=$User->add($upProblemData);
		if($count)
			$this->success('success','showTrainProblemList');
		else $this->error('fail','showTrainProblemList');
	}
	public function showTrainUpData(){
		$this->assign('problemdata',$_GET);
		$this->display();
	}
	public function trainUpload(){
		$problem_id=$_POST['id'];
		$path='Data/Train/problems/'.$problem_id;
		if(!file_exists($path)) 
		{
			mkdir($path);
			chmod($path, 0777);
		}
		$this->deleteOleFile($path.'/');//删除目录下所有文件
		//设置上传参数
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 83886080 ;// 设置附件上传大小
		$upload->uploadReplace=true;
		$upload->savePath =  $path.'/';// 设置附件上传目录
		$upload->saveRule='';
		//$_FILES['input_path']['name'] = 'in';
		//$_FILES['output_path']['name'] = 'out';
		//$_FILES['file_path']['name'] = 'data.zip';
		
		if(!$upload->upload()) {// 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
		}else{// 上传成功 获取上传文件信息
			$info =  $upload->getUploadFileInfo();
			$zip = new ZipArchive();//新建一个对象
			//dump($info);
			$filepath=$upload->savePath.$info[0]['name'];
			//dump($filepath);
			//die;
			if ($zip->open($filepath) === TRUE)
			{
			    if(($zip->numFiles)%2==1){
			    	$this->error("数据文件格式错误！");
			    }
			    
				$zip->extractTo($upload->savePath);//假设解压缩到在当前路径下images文件夹的子文件夹php
				
				if($this->fileIsOk($upload->savePath,$zip->numFiles))
					$this->error("数据文件格式错误！");
				
				$problemData=M('problem')
							->where(array('id'=>$problem_id))
							->find();
				$problemData['case_number']=($zip->numFiles)/2;
				M('problem')
						->where(array('id'=>$problem_id))
						->save($problemData);
				$zip->close();//关闭处理的zip文件
			}
			$levelMsgId=M('train_problem')->where(array('id'=>$problem_id))->find()['level_msg_id'];
			$this->success('success!',U('showTrainProblemList',array('id'=>$levelMsgId)));
		}
	}
	public function updateSubmit(){
		$ladder_judge_detail=M('ladder_judge_detail');
		$userProblemData=M('ladder_user_problem')->select();
		//dump($userProblemData);
		foreach($userProblemData as $k => $v){
			$problemScore=M('ladder_contest_problem')->where(array('problem_id'=>$userProblemData[$k]['problem_id']))->find()['score'];
			//dump($problemScore);
			$judgeDetail=M('ladder_judge_detail')->where(array('user_problem_id'=>$userProblemData[$k]['id']))->select();
			$cnt=count($judgeDetail);
			$judgeScore=0;
			foreach($judgeDetail as $k1 => $v1){
				if($judgeDetail[$k1]['judge_status']==0) $judgeScore=$judgeScore+$problemScore/$cnt;
				$judgeData['group_score']=$problemScore/$cnt;
				//dump($judgeData);
				//dump($userProblemData[$k]['id']);
				$ladder_judge_detail->where(array('user_problem_id'=>$userProblemData[$k]['id']))->save($judgeData);
			}
			$record['score']=$judgeScore;
			M('ladder_user_problem')->where(array('id'=>$userProblemData[$k]['id']))->save($record);
		}
		$user=M('user')->select();
		$user_problem=M('user_problem');
		$problem=M('problem');
		$User=M('user');
		$ladder_user_problem=M('ladder_user_problem');
		//dump($user);
		foreach($user as $k => $v){
			$user_problem_data=$user_problem->where(array('user_id'=>$user[$k]['id'],'judge_status'=>0))->distinct('problem_id')->select();
			//dump($user_problem_data);
			$problem_score=0;
			foreach($user_problem_data as $k1 => $v1){
				$difficulty=$problem->where(array('id'=>$user_problem_data[$k1]['problem_id']))->find()['difficulty'];
				$problem_score=$problem_score+$difficulty*2+5;
			}
			//dump($problem_score);
			$library_score['library_score']=$problem_score;
			$User->where(array('id'=>$user[$k]['id']))->save($library_score);
			
			
			$ladder_problem_data=$ladder_user_problem->where(array('user_id'=>$user[$k]['id']))->distinct('problem_id')->select();
			$problem_score=0;
			foreach($ladder_problem_data as $k1 => $v1){
				$problem_score=$problem_score+$ladder_problem_data[$k1]['score'];
			}
			$ladder_score['ladder_score']=$problem_score;
			$User->where(array('id'=>$user[$k]['id']))->save($ladder_score);
		}
		//die;
		$this->redirect('LadderAdmin/showContestListPage');
	}
	public function showBulletinPage(){
		$bulletinData=M('bulletin')->order('creat_time desc')->select();
		foreach($bulletinData as $k => $v){
			$bulletinData[$k]['anthor']=M('user')->where(array('id'=>$bulletinData[$k]['anthor_id']))->find()['username'];
		}
		$this->assign('bulletinData',$bulletinData);
		$this->display();
	}
	public function showAddBulletinPage(){
		$this->display();
	}
	public function addBulletin(){
		$userinfo=session('userinfo');
		$bulletinData['content']=$_POST['content'];
		$bulletinData['creat_time']=time();
		$bulletinData['anthor_id']=$userinfo['id'];
		M('bulletin')->add($bulletinData);
		$this->redirect('Admin/showBulletinPage');
	}
	public function showViolationQueryPage(){
		$value=$_POST['value'];
		$where['id']  = $value;
		$where['area']  = array('like','%'.$value.'%');
		$where['ip']  = array('like','%'.$value.'%');
		//$where['login_time']  = array('like','%'.$value.'%');
		$where['information']  = array('like','%'.$value.'%');
		$where['_logic'] = 'or';
		$violationData=M('violation')->order('id desc')->where($where)->select();
		foreach($violationData as $k => $v){
			$violationData[$k]['username']=
				M('user')->where(array('id'=>$violationData[$k]['user_id']))->find()['username'];
		}
		$this->assign('violationData',$violationData);
		$this->display();
	}
	public function showUserSolveProblemPage(){
		$groupData=M('group')->select();
		$peopleCnt=0;
		if($_POST['group']){
			$peopleId=M('user_group')->where(array('group_id'=>$_POST['group']))->field('user_id')->select();
			foreach($peopleId as $k => $v){
				$peopleId[$k]=$peopleId[$k]['user_id'];
			}
			$map['user_id']  = array('in',$peopleId);
			$peopleCnt=count($peopleId);
		}
		if($_POST['problemId']){
			$pid=M('problem')->where(array('problem_mark'=>$_POST['problemId']))->find()['id'];
			$this->assign('pid',$_POST['problemId']);
			$map['problem_id']  = $pid;
		}else {
			$map['problem_id']  = -1;
		}
		$Submissions=M('user_problem')->where($map)->select();
		$submissionCnt=count($Submissions);
		$map['judge_status']=0;
		$Submissions=M('user_problem')->where($map)->Distinct(true)->field('user_id')->select();
		$acPeopleCnt=count($Submissions);
		$NotAcPeopleCnt=$peopleCnt-$acPeopleCnt;
		$acCnt=M('user_problem')->where($map)->count();
		$firstName=M('user_problem')->where(array('problem_id'=>$pid,'user_id'=>array('in',$peopleId),'judge_status'=>0))->find();
		$firstName=M('user')->where(array('id'=>$firstName['user_id']))->find();
		$user_problem=M('user_problem');
		$user=M('user');
		$allUserData=array();
		foreach($peopleId as $k => $v){
//			dump($k);
			$userId=$peopleId[$k];
			if($user_problem->where(array('user_id'=>$userId,'judge_status'=>0))->find()){
				$allUserData[$k]['status']=0;
			}else {
				$allUserData[$k]['status']=1;
			}
			$allUserData[$k]['user_id']=$userId;
			$allUserData[$k]['username']=M('user')->where(array('id'=>$userId))->find()['username'];
		}
//		dump($allUserData);
		$this->assign('groupData',$groupData);
		$this->assign('peopleCnt',$peopleCnt);
		$this->assign('submissionCnt',$submissionCnt);
		$this->assign('acPeopleCnt',$acPeopleCnt);
		$this->assign('NotAcPeopleCnt',$NotAcPeopleCnt);
		$this->assign('acCnt',$acCnt);
		$this->assign('firstName',$firstName);
		$this->assign('allUserData',$allUserData);
		$this->display();
	}
}