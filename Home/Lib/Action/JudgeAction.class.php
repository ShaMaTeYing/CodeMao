<?php
// 本类由系统自动生成，仅供测试用途
class JudgeAction extends BaseAction {
	public function getStatusArray(){
		$status=array('Accepted','Wrong Answer','Time Limit Exceeded',
		'Memory Limit Exceeded','Runtime Error','Compilation Error',
		'Output Limit Exceeded','Input Limit Exceeded','pending',
		'Compiling','runing','All');
		$results=array(array());
		foreach($status as $key => $value){
			$results[$key]['index']=$key;
			$results[$key]['status']=$value;
		}
		return $results;
	}
	public function getLanguage(){
		$language=array('All','C++','C','PASCAL');
		$results=array(array());
		foreach($language as $key => $value){
			$results[$key]['index']=$value;
			$results[$key]['status']=$value;
		}
		return $results;
	}
	public function getValue($mypost,$myget,$key){
		if(isset($mypost[$key])) $ans=$mypost[$key];
		else if(isset($myget[$key])) $ans=$myget[$key];
		return $ans;
	}
	public function getAcProblemId(){
		$userinfo = session('userinfo');
//		dump($userinfo);
		$ids=M('user_problem')->where(array('user_id'=>$userinfo['id'],'judge_status'=>0))
			->distinct(true)->field('problem_id')->select();
		foreach($ids as $k => $v){
			$ids[$k]=$ids[$k]['problem_id'];
		}
//		dump($ids);
//		dump(array("12","34","56"));
		return $ids;
	}
	public function showRealTimeEvaluation(){
		$ids=$this->getAcProblemId();
		$statusArray=$this->getStatusArray();
		$languageArray=$this->getLanguage();
		$this->assign("statusArray",$statusArray);
		$this->assign("languageArray",$languageArray);
		$userinfo = session('userinfo');
		$problemId=$this->getValue($_POST,$_GET,'problemId');
		$anthor=$this->getValue($_POST,$_GET,'anthor');
		$language=$this->getValue($_POST,$_GET,'language');
		$judgeResults=$this->getValue($_POST,$_GET,'status');
		$parmCnt=0;
		if($language&&$language!="All") {
			$where['language']=$language;
			$parmCnt=$parmCnt+1;
		}
		if(isset($judgeResults)) {
			if($judgeResults!=11){
				$where['judge_status']=$judgeResults;
				$parmCnt=$parmCnt+1;
			}
		}
		if($problemId) {
			
			//$where['problem_id']  = $problemId;
			$where['problem_id']  =M('problem')->where(array('problem_mark'=>$problemId))->find()['id'];
			
			$parmCnt=$parmCnt+1;
		}
		if($anthor) {
			$where['nickname']  = array('like','%'.$anthor.'%');
			$parmCnt=$parmCnt+1;
		}
		if($parmCnt>1) $where['_logic'] = 'and';
		
		$User = M('user_problem'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count = $User->where($where)->count();// 查询满足要求的总记录数
		$Page  = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $k=>$v){
			$list[$k]['problem_mark']=M('problem')->where(array('id'=>$list[$k]['problem_id']))->find()['problem_mark'];
			if(in_array($list[$k]['problem_id'],$ids)||$userinfo['root']>0){
				$list[$k]['look_code']=1;
			}else {
				$list[$k]['look_code']=0;
			}
		}
//		dump($userinfo);
		//dump($Page);
		
		//$list['username']=$userinfo;
		if(!$language) $language="All";
		//dump($language);
		if(!isset($judgeResults)) $judgeResults=11;
		//dump($judgeResults);
		$this->assign("lan",$language);
		$this->assign("sta",$judgeResults);
		$this->assign("pid",$problemId);
		$this->assign("ant",$anthor);
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$userinfo = session('userinfo');
		$this->myId = $userinfo['id'];
		$this->myRoot=$userinfo['root'];
		$this->display(); // 输出模板
		
	}
	private function getfiles( $path , &$files = array() )
	{
	    if ( !is_dir( $path ) ) return 0;
	    $handle = opendir( $path );
	    while ( false !== ( $file = readdir( $handle ) ) ) {
	        if ( $file != '.' && $file != '..' ) {
	            $path2 = $path . '/' . $file;
	            if ( is_dir( $path2 ) ) {
	                getfiles( $path2 , $files );
	            } else {
	                if ( preg_match( "/\.(in)$/i" , $file ) ) {
	                    $files[] = $path2;
	                }
	            }
	        }
	    }
	    return count($files);
	}
	private function getCaseNumber($id){
		$datapath='Data/Library/problems/'.$id;
		return $this->getfiles($datapath);
	}
	public function onlineJudge(){
		$userinfo=session('userinfo');
		if(!$userinfo){
			$this->error("别调皮啦，提交代码前，请先登录!",U('User/showLogin'));
		}
		if(strstr($_POST['editor'],"freopen")){
			$this->error("代码中含有freopen，禁止提交!",U('Problem/showProblemList'));
		}
		if(strstr($_POST['editor'],"fopen")){
			$this->error("代码中含有fopen，禁止提交!",U('Problem/showProblemList'));
		}
		$problemData=M('problem')->where('id='.$_POST['problemID'])->find();
		if(!$problemData){
			$this->error('非法操作!!!!!!!');
		}
		$caseNumber=$this->getCaseNumber($_POST['problemID']);
		if($caseNumber==0){
			$this->error("此题暂无测试数据，禁止提交!",U('Problem/showProblemList'));
		}
		$number=intval($problemData['time_limit'],10);
		$timeLimit=$number;
		$memoryLimit=32768;
		$userinfo = session('userinfo');
		$code='Data/Library/code';
		if(!file_exists($code)) {
			mkdir($code);
			chmod($code, 0777);
		}
		if(!file_exists($code.'/'.$userinfo['username'])) {
			mkdir($code.'/'.$userinfo['username']);
			chmod($code.'/'.$userinfo['username'], 0777);
		}
		
		//dump($_POST);
		
		$lan=$_POST['language'];
		session('lastLan',$lan);
		$ext='';
		if($lan=='C++'){
			$ext='.cpp';
		}else if($lan=='C'){
			$ext='.c';
		}else if($lan=='PASCAL') {
			$ext='.pas';
		}else if($lan=='PHP'){
			$ext='.php';
		}
		

		$condition['user_id']=$userinfo['id'];
		$condition['problem_id']=$_POST['problemID'];
		$submitSum=M('user_problem')->where($condition)->Count();
		$submitSum=$submitSum+1;
		//dump($submitSum);
		
		$filename = $code.'/'.$userinfo['username'].'/'.$_POST['problemID'].'_'.$submitSum.$ext;
		$word =$_POST['editor'];
		file_put_contents($filename, $word);
		chmod($filename, 0777);

		$destFile=$filename;
		$file=$_POST['problemID'];
	
		
		$verdict=8;//设置成待判状态

		$resultData['user_id']=$userinfo['id'];
		$resultData['problem_id']=$_POST['problemID'];
		$resultData['submit_time']=time();
		$resultData['judge_status']=$verdict;
		$resultData['exe_time']=0;
		$resultData['exe_memory']=0;
		$resultData['code_len']=strlen($_POST['editor']);
		$resultData['language']=$_POST['language'];
		$resultData['nickname']=$userinfo['nickname'];
		$resultData['filepath']=$filename;
		//dump($resultData);
		
		
			
		$message=M('user_problem');
		$userProblemId=$message->add($resultData);
		
		//$caseNumber
		$judgeDetail['user_problem_id']=$userProblemId;
		$judgeDetail['judge_status']=8;
		$judgeDetail['exe_time']=0;
		$judgeDetail['exe_memory']=0;
		$judgeDetail['score']=0;
		$judgeDetail['group_score']=intval(100/$caseNumber);
		for($i=1;$i<=$caseNumber;$i++){
			$caseInputPath='Data/Library/problems'.'/'.$_POST['problemID'].'/'.$i.'.in';
			$caseOutputPath='Data/Library/problems'.'/'.$_POST['problemID'].'/'.$i.'.out';
			$judgeDetail['group_id']=$i;
			$judgeDetail['input_file_path']=$caseInputPath;
			$judgeDetail['output_file_path']=$caseOutputPath;
			if($i==$caseNumber) $judgeDetail['group_score']=100-($caseNumber-1)*intval(100/$caseNumber);
			M('judge_detail')->add($judgeDetail);
		}
		$tip = M('tips')->where(array('user_id'=>$userinfo['id'],'problem_id'=>$_POST['problemID']))->find();
		dump($tip);
//		die;
		if(!$tip){
			$tips['user_id']=$userinfo['id'];
			$tips['problem_id']=$_POST['problemID'];
			$tips['tip']=0;
			M('tips')->add($tips);
		}
		$this->redirect('Judge/showRealTimeEvaluation');
	}
	public function showUserCode(){
		$id=$_GET['id'];
		$condition['id']=$id;
		$userinfo=session('userinfo');
		
//		if($userinfo['root']==0&&$userinfo['id']!=$_GET['userId']){
//			saveViolationMessage('偷看源代码 运行ID'.$id);
//			$this->error('不许偷看别人的源代码哦！',U("Judge/showRealTimeEvaluation"));
//		}
		$data[0]=M('user_problem')->where($condition)->find();
		$res=M('user_problem')->where(array('problem_id'=>$data[0]['problem_id'],'user_id'=>$userinfo['id'],'judge_status'=>0))->find();
		//dump($data);
		
		$filename = $data[0]['filepath'];
//		if($data[0]['user_id']!=$_GET['userId'] && $userinfo['root']==0){
//			saveViolationMessage('偷看源代码 运行ID'.$id);
//			$this->error('不许偷看别人的源代码哦！',U("Judge/showRealTimeEvaluation"));
//		}
		if($userinfo['root']==0&&!$res&&$data[0]['user_id']!=$userinfo['id']){
			saveViolationMessage('偷看源代码 运行ID'.$id);
			$this->error('不许偷看别人的源代码哦！',U("Judge/showRealTimeEvaluation"));
		}
		$contents=file_get_contents($filename);
		$contents = htmlspecialchars($contents);
		$this->assign('code',$contents);
		$this->assign('judgeData',$data);
		$this->display();
	}
	public function showJudgeDetail(){
		$userProblemData=M('user_problem')
				->where(array('id'=>$_GET['id']))
				->find();
		$judgeData=M('judge_detail')
				->where(array('user_problem_id'=>$userProblemData['id']))
				->select();
		//dump($judgeData);
		$problemData=M('problem')
				->where(array('id'=>$userProblemData['problem_id']))
				->find();
		if($userProblemData['judge_status']==0)
		    $allScore=2*$problemData['difficulty']+5;
		else $allScore=0;
		$userinfo = session('userinfo');
		$this->assign('score',$allScore);
		$this->assign('myRoot',$userinfo['root']);
		$this->assign('judgeData',$judgeData);
		$this->assign('userProblemData',$userProblemData);
		$this->assign('problemData',$problemData);
		$this->display();
	}
	function checkLookDataRoot($filePath){
		$userinfo=session('userinfo');
		if($userinfo['root']<2){
			saveViolationMessage('非法访问 题库数据'.$filePath);
			$this->error("非法访问，违规行为将被记录!",U('User/showLogin'));
			
		}
	}
	public function showTestData(){
		//dump($_GET);
		$data=M('judge_detail')->where(array('id'=>$_GET['id']))->find();
		if($_GET['op']) $filePath=$data['output_file_path'];
		else $filePath=$data['input_file_path'];
		$this->checkLookDataRoot($filePath);
		$contents=file_get_contents($filePath);
		$contents = htmlspecialchars($contents);
		$this->assign('text',$contents);
		$this->display();
	}
}