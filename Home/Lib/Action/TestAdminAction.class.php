<?php
// 本类由系统自动生成，仅供测试用途
class TestAdminAction extends BaseAction {
	/* 构造函数 */
	function _initialize(){
		$userinfo=session('userinfo');
		if(!$userinfo||$userinfo['root']<1){
			$this->error("非法访问，请先登录!",U('User/showLogin'));
		}
		$login=M('user')->where(array('id'=>$userinfo['id']))->find();
		$loginStatus=session('loginStatus');
	   //控制切换登录窗口
		$this->assign('loginStatus',session('loginStatus')?session('loginStatus'):0);
		if(session('loginStatus'))//登录成功则传值给模板变量
		{
			$this->assign('userinfoData',session('userinfo'));
		}
	}
	/*显示题库管理主界面*/
	public function showProblemLibrary(){
		$User = M('test_problem'); // 实例化User对象
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
			$datapath='Data/Test/problems/'.$list[$key]['id'].'/1.in';
			//dump($datapath);
			if(file_exists($datapath)) $list[$key]['dataStatus']=1;
			else $list[$key]['dataStatus']=0;
			
		}
		$userinfo=session('userinfo');
		$this->assign('userinfo',$userinfo);
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
		
		$User = M("test_problem");
		foreach($_POST as $key=>$value){
			$_POST[$key]=htmlspecialchars($value);
		}
		$labelString=$_POST['label'];
		$labelString=$this->trimall($labelString);
		$labelData=trim($labelString);
		$problemId=$User->max('id');
		$_POST['id']=$problemId+1;
		$_POST['label']=$labelData;
		$count=$User->add($_POST);
		if($count)
			$this->success('success','showProblemLibrary');
		else $this->error('fail','showProblemLibrary');
	}
	
	/*删除题目*/
	public function deleteProblem(){
		$id=$_GET['id'];
		$User = M("test_problem"); // 实例化User对象
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
		$upload->savePath =  'Data/Test/describe/';// 设置附件上传目录
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
		$User = M("test_problem");
		
		$labelString=$upProblemData['label'];
		$labelString=$this->trimall($labelString);
		$labelData=trim($labelString);
		$problemId=$User->max('id');
		$upProblemData['id']=$problemId+1;
		$upProblemData['label']=$labelData;
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
	/*上传文件*/
	public function upload(){
		
		$problem_id=$_POST['id'];
		$path='Data/Test/problems/'.$problem_id;
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
		$data=M('test_problem')->where('id='.$id)->find();
		$labelData=$data['label'];
		$this->assign('labelData',$labelData);
		$this->assign('data',$data);
		$this->display();
	}
	/*修改题目*/
	public function modifyProblemData(){
		//dump($_POST);
		
		foreach($_POST as $key=>$value){
			$_POST[$key]=htmlspecialchars($value);
		}
		$data=$_POST;
		$labelString=$data['label'];
		$labelData=trim($labelString);
		$_POST['label']=$labelData;
		$count=M('test_problem')->where('id='.$_POST['id'])->save($_POST);
		if($count>0){
			$this->success('success!','showProblemLibrary');
		}else {
			$this->error('fail','showProblemLibrary');
		}
	}
	
	public function reJudge(){
		//dump($_GET);
		$data['judge_status']=8;
		$allJudgeRecord=M('test_user_problem')->where('problem_id='.$_GET['id'])->save($data);
		$this->success('success!',U('TestAdmin/showProblemLibrary'));
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
	public function copy_dir($src,$dst) {
	  $dir = opendir($src);
	  @mkdir($dst);
	  while(false !== ( $file = readdir($dir)) ) {
	    if (( $file != '.' ) && ( $file != '..' )) {
	      if ( is_dir($src . '/' . $file) ) {
	        $this->copy_dir($src . '/' . $file,$dst . '/' . $file);
	        continue;
	      }
	      else {
	        copy($src . '/' . $file,$dst . '/' . $file);
	      }
	    }
	  }
	  closedir($dir);
	}
	public function deleteTestUserProblemRecord($problemId){
		$allUserProblemRecord=M('test_user_problem')->where(array('problem_id'=>$problemId))->select();
		$test_judge_detail=M('test_judge_detail');
		$test_user_problem=M('test_user_problem');
		foreach($allUserProblemRecord as $k => $v){
			$userProblemId=$allUserProblemRecord[$k]['id'];
			$test_judge_detail->where(array('user_problem_id'=>$userProblemId))->delete();
			$test_user_problem->where(array('id'=>$userProblemId))->delete();
		}
	}
	public function addTestToProblem(){
		$problemData=M('test_problem')->where(array('id'=>$_GET['id']))->find();
		//dump($problemData);
		$labelString=$problemData['label'];
		$labelString=$this->trimall($labelString);
		$labelString=trim($labelString);
		$labelData=explode(";", $labelString);
		$problemId=M('problem')->max('id');
		for($i=0;$i<count($labelData);$i++){
			$where['label_name']=$labelData[$i];
			$cnt=M('label_info')->where($where)->count();
			if($cnt==0){
				$data['label_name']=$labelData[$i];
				$data['status']=0;
				M('label_info')->data($data)->add();
			}
			$labelId=M('label_info')->where($where)->find();
			$problemLabelData['problem_id']=$problemId+1;
			$problemLabelData['label_id']=$labelId['id'];
			M('problem_label')->data($problemLabelData)->add();
		}
		
		unset($problemData['label']);
		unset($problemData['id']);
		$problemId=$problemId+1;
		$src="Data/Test/problems/".$_GET['id'];
		$dst="Data/Library/problems/".$problemId;
		//dump($src);dump($dst);
		$this->copy_dir($src,$dst);
		M('test_problem')->delete($_GET['id']);
		$this->deleteTestUserProblemRecord($_GET['id']);
		$count=M('problem')->add($problemData);
		if($count)
			$this->success('success',U('showProblemLibrary'));
		else $this->error('fail',U('showProblemLibrary'));
	}
	public function specialTestToProblem($form_poblem_id,$to_problem_id){
		$problemData=M('test_problem')->where(array('id'=>$form_poblem_id))->find();
		//dump($problemData);
		$labelString=$problemData['label'];
		$labelString=$this->trimall($labelString);
		$labelString=trim($labelString);
		$labelData=explode(";", $labelString);
		$problemId=$to_problem_id;
		for($i=0;$i<count($labelData);$i++){
			$where['label_name']=$labelData[$i];
			$cnt=M('label_info')->where($where)->count();
			if($cnt==0){
				$data['label_name']=$labelData[$i];
				$data['status']=0;
				M('label_info')->data($data)->add();
			}
			$labelId=M('label_info')->where($where)->find();
			$problemLabelData['problem_id']=$problemId;
			$problemLabelData['label_id']=$labelId['id'];
			M('problem_label')->data($problemLabelData)->add();
		}
		
		unset($problemData['label']);
		unset($problemData['id']);
		$problemId=$problemId;
		$src="Data/Test/problems/".$form_poblem_id;
		$dst="Data/Library/problems/".$to_problem_id;
		//dump($src);dump($dst);
		$this->copy_dir($src,$dst);
		M('test_problem')->delete($_GET['id']);
		$problemData['id']=$problemId;
		$count=M('problem')->add($problemData);
		if($count)
			$this->success('success',U('showProblemLibrary'));
		else $this->error('fail',U('showProblemLibrary'));
	}
	public function batchAddProbelm(){
		$start_problem_id=10001;
		$problem_id_data=array(0=>array(23,71),1=>array(116,122),2=>array(107,115),3=>array(72,106));
		foreach($problem_id_data as $k => $v){
			$start_id=$problem_id_data[$k][0];
			$end_id=$problem_id_data[$k][1];
			for($i=$start_id;$i<=$end_id;$i++){
				$this->specialTestToProblem($i,$start_problem_id);
				$start_problem_id++;
			}
		}
	}
}