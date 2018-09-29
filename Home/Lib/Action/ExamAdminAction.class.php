<?php
// 本类由系统自动生成，仅供测试用途
class ExamAdminAction extends AdminAction {
	public function index(){
		$User = M('contest_list'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count = $User->count();// 查询满足要求的总记录数
		$Page  = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		//$where['is_visible']=1;
		$list = $User->order('start_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//$list['username']=$userinfo;
		foreach($list as $key=>$value){
			
			if(intval($value['start_time'])<time() && time()<intval($value['end_time'])){
				$list[$key]['status']="正在比赛";
			}else if(time()<intval($value['start_time'])){
				$list[$key]['status']="等待开始";
			}else {
				$list[$key]['status']="比赛结束";
			}
			$list[$key]['anthor']=M('user')->where(array('id'=>$list[$key]['user_id']))->find()['nickname'];
			if($list[$key]['type']==0){
				$list[$key]['type']='公开';
			}else if($list[$key]['type']==1){
				$list[$key]['type']='私有';
			}else if($list[$key]['type']==2){
				$list[$key]['type']='密码';
			}
		}
		
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('nowTime',time());
	
		$userinfo = session('userinfo');
		$this->myId = $userinfo['id'];
		$this->display(); // 输出模板
	}
	protected function getType(){
		$type[0]['index']=0;$type[0]['status']='公开';
		$type[1]['index']=1;$type[1]['status']='私有';
		$type[2]['index']=2;$type[2]['status']='密码';
		return $type;
	}
	public function showAddExam(){
		
		$nowTime = date("Y-m-d H:i",time());
		//dump($nowTime);
		$type=$this->getType();
		$this->assign('nowTime',$nowTime);
		$this->assign('type',$type);
		$this->assign('sta',0);
		$this->display();
	}
	public function addExam(){
		$_POST['start_time']=strtotime($_POST['start_time']);
		$_POST['end_time']=strtotime($_POST['end_time']);
		$userinfo=session('userinfo');
		$_POST['user_id']=$userinfo['id'];
		if(isset($_POST['is_visible'])) $_POST['is_visible']=0;
		else $_POST['is_visible']=1;
		$res=M('contest_list')->add($_POST);
		if($res){
			$this->success('添加成功！',U('ExamAdmin/index'));
		}else {
			$this->error('添加失败！',U('ExamAdmin/index'));
		}
	}
	public function showModifyExamPage(){
		$contestData=M('contest_list')->where(array('id'=>$_GET['id']))->find();
		//dump($contestData);
		$contestData['start_time']=date("Y-m-d H:i",$contestData['start_time']);
		$contestData['end_time']=date("Y-m-d H:i",$contestData['end_time']);
		$type=$this->getType();
		$this->assign('type',$type);
		$this->assign('contestData',$contestData);
		$this->display();
	}
	public function modifyExam(){
		$contestId=$_POST['id'];
		$_POST['start_time']=strtotime($_POST['start_time']);
		$_POST['end_time']=strtotime($_POST['end_time']);
		$userinfo=session('userinfo');
		$_POST['user_id']=$userinfo['id'];
		if(isset($_POST['is_visible'])) $_POST['is_visible']=0;
		else $_POST['is_visible']=1;
		unset($_POST['id']);
		
		$res=M('contest_list')->where(array('id'=>$contestId))->save($_POST);
		if($res){
			$this->success('修改成功！',U('ExamAdmin/index'));
		}else {
			$this->error('修改失败！',U('ExamAdmin/index'));
		}
	}
	public function switchExamStatus(){
		$is_visible=M('contest_list')->where(array('id'=>$_GET['id']))->find()['is_visible'];
		$where['is_visible']=1-$is_visible;
		$res=M('contest_list')->where(array('id'=>$_GET['id']))->save($where);
		if($res){
			$this->success('修改成功！',U('ExamAdmin/index'));
		}else {
			$this->error('修改失败！',U('ExamAdmin/index'));
		}
	}
	public function showProblemListPage(){
		$contestData=M('contest_list')->where(array('id'=>$_GET['id']))->find();
		$problemData=M('contest_problem')->where(array('contest_id'=>$_GET['id']))->select();
		foreach($problemData as $key => $value){
			$datapath='Data/Contest/problems/'.$problemData[$key]['id'].'/1.in';
			//dump($datapath);
			if(file_exists($datapath)) $problemData[$key]['dataStatus']=1;
			else $problemData[$key]['dataStatus']=0;
		}
		$this->assign('nowTime',time());
		$this->assign('contestData',$contestData);
		$this->assign('problemData',$problemData);
		$this->display();
	}
	public function showAddProblemPage(){
		$problemNumber=M('contest_problem')->where(array('contest_id'=>$_GET['id']))->count();
		$str="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$problem_mark=substr($str,$problemNumber,1);
		$this->assign('problem_mark',$problem_mark);
		$this->assign('contestId',$_GET['id']);
		$this->display();
	}
	public function addProblem(){
		//dump($_POST);
		$problemData=$_POST;
		$problemData['submissions']=0;
		$problemData['accepted']=0;
		$problemData['status']=1;
		$res=M('contest_problem')->add($problemData);
		if($res){
			$this->success('添加成功！',U('ExamAdmin/showProblemListPage',array('id'=>$problemData['contest_id'])));
		}else {
			$this->error('添加失败！',U('ExamAdmin/showProblemListPage',array('id'=>$problemData['contest_id'])));
		}
	}
	public function showModifyProblemPage(){
		$id=$_GET['id'];
		//dump($_GET);
		$probelmData=M('contest_problem')->where('id='.$id)->find();
		$this->assign('probelmData',$probelmData);
		$this->assign('contestId',$_GET['contest_id']);
		$this->display();
	}
	public function modifyProblem(){
		$problemData=$_POST;
		unset($problemData['id']);
		$res=M('contest_problem')->where(array('id'=>$_POST['id']))->save($problemData);
		if($res){
			$this->success('修改成功！',U('ExamAdmin/showProblemListPage',array('id'=>$problemData['contest_id'])));
		}else {
			$this->error('修改失败！',U('ExamAdmin/showProblemListPage',array('id'=>$problemData['contest_id'])));
		}
	}
	public function switchProblemStatus(){
		$status=M('contest_problem')->where(array('id'=>$_GET['id']))->find()['status'];
		$where['status']=1-$status;
		$res=M('contest_problem')->where(array('id'=>$_GET['id']))->save($where);
		
		if($res){
			$this->redirect('ExamAdmin/showProblemListPage',array('id'=>$_GET['contest_id']));
		}else {
			$this->redirect('ExamAdmin/showProblemListPage',array('id'=>$_GET['contest_id']));
		}
	}
	public function showUpLoadProblemDataPage(){
		$problemData=M('contest_problem')->where(array('id'=>$_GET['id']))->find();
		$this->assign('problemData',$problemData);
		$this->assign('contestId',$_GET['contest_id']);
		$this->display();
	}
		//扫描目录下的所有文件
	public function my_scandir($dir){  
	 $files=array();  
	 if(is_dir($dir)){  
	  if($handle=opendir($dir)){  
	   while(($file=readdir($handle))!==false){  
	    if($file!='.' && $file!=".."){  
	     if(is_dir($dir.$file)){  
	      $files[$file]=my_scandir($dir.$file);  
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
	public function upLoadProblemData(){
		$problem_id=$_POST['id'];
		$path='Data/Contest/problems/'.$problem_id;
		if(!file_exists($path)) 
		{
			mkdir($path);
			chmod($path, 0777);
		}
		$this->deleteOleFile($path.'/');//删除目录下所有文件
		//设置上传参数
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 8388608 ;// 设置附件上传大小
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
			$this->redirect('showProblemListPage', array('id' => $_POST['contest_id']));
			
		}
	}
	
	public function showProblemPage(){
		$problemId=$_GET['id'];
		//echo "problemId".$problemId;
		$arr=M('contest_problem')->find($problemId);
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
	public function reJudge(){
		//dump($_GET);
		$data['judge_status']=8;
		$allJudgeRecord=M('contest_user_problem')->where(array('problem_id'=>$_GET['id']))->save($data);
		$this->success('success!',U('ExamAdmin/showProblemListPage'));
	}
}