<?php
// 本类由系统自动生成，仅供测试用途
class TrainAction extends BaseAction {
	function _initialize(){
		$userinfo=session('userinfo');
		if(!$userinfo){
			$this->error("请先登录!",U('User/showLogin'));
		}
//		if($userinfo['root']<1){
//			$this->error("您无权查看!",U('Problem/showProblemList'));
//		}
		$login=M('user')->where(array('id'=>$userinfo['id']))->find();
		$loginStatus=session('loginStatus');
	   //控制切换登录窗口
		$this->assign('loginStatus',session('loginStatus')?session('loginStatus'):0);
		if(session('loginStatus'))//登录成功则传值给模板变量
		{
			$this->assign('userinfoData',session('userinfo'));
		}
	}
	public function getTrainProblemMessage($levelMsg){
		$levelId=session('levelId');
		//$levelMsg=M('level_msg')->where(array('level_id'=>$levelId,'status'=>0))->order('priority asc')->select();
		$levelData=M('level')->where(array('id'=>$levelId))->find();
		$train_problem=M('train_problem');
		$train_user_problem=M('train_user_problem');
		$userinfo=session('userinfo');
		foreach($levelMsg as $k => $v){
			$levelMsg[$k]['problem_number']=$train_problem->where(array('level_msg_id'=>$levelMsg[$k]['id'],'status'=>1))->count();
			$where['user_id']=$userinfo['id'];
			$where['judge_status']=0;
			$where['level_msg_id']=$levelMsg[$k]['id'];
			$tmp=$train_user_problem->where($where)->distinct('problem_id')->field('problem_id')->select();
			//dump($tmp);
			foreach($tmp as $k1 => $v1){
				$temp=$train_problem->where(array('id'=>$tmp[$k1]['problem_id'],'status'=>0))->find();
				if($temp) unset($tmp[$k1]);
			}
			$levelMsg[$k]['pass_number']=count($tmp);
		}
		return $levelMsg;
	}
	public function index(){
		$levelData=M('level')->where(array("status"=>0))->select();
		$levelData[0]['backgroud']='red';$levelData[1]['backgroud']='green';$levelData[2]['backgroud']='yellow';
		if(isset($_GET['id'])) $levelId=$_GET['id'];
		else $levelId=1;
		session('levelId',$levelId);
		$nowLevelData=M('level')->where(array("id"=>$levelId))->find()['entrance_title'];
		$levelMsgData=M('level_msg')->where(array('level_id'=>$levelId))->order('priority asc')->select();
		$levelMsgData=$this->getTrainProblemMessage($levelMsgData);
		//dump($levelMsgData);
		$this->assign('list',$levelMsgData);
		$this->assign('nowLevelData',$nowLevelData);
		$this->assign('levelData',$levelData);
		//$this->display();
		
//		$User = M('level_msg'); // 实例化User对象
//		import('ORG.Util.Page');// 导入分页类
//		$count      = $User->where(array('level_id'=>$levelId))->count();// 查询满足要求的总记录数
//		$Page       = new Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
//		$show       = $Page->show();// 分页显示输出
//		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
//		$list = $User->where(array('level_id'=>$levelId))->order('priority')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($list);
		//$list=$this->getTrainProblemMessage($list);
		//dump($list);
		//$this->assign('list',$list);// 赋值数据集
		//$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板

	}
	public function showTaskList(){
		$levelMsg=M('level_msg')->where(array('level_id'=>$_GET['id'],'status'=>0))->order('priority asc')->select();
		$levelData=M('level')->where(array('id'=>$_GET['id']))->find();
		session('levelId',$_GET['id']);
		$train_problem=M('train_problem');
		$train_user_problem=M('train_user_problem');
		$userinfo=session('userinfo');
		foreach($levelMsg as $k => $v){
			$levelMsg[$k]['problem_number']=$train_problem->where(array('level_msg_id'=>$levelMsg[$k]['id'],'status'=>1))->count();
			$where['user_id']=$userinfo['id'];
			$where['judge_status']=0;
			$where['level_msg_id']=$levelMsg[$k]['id'];
			$tmp=$train_user_problem->where($where)->distinct('problem_id')->field('problem_id')->select();
			//dump($tmp);
			foreach($tmp as $k1 => $v1){
				$temp=$train_problem->where(array('id'=>$tmp[$k1]['problem_id'],'status'=>0))->find();
				if($temp) unset($tmp[$k1]);
			}
			$levelMsg[$k]['pass_number']=count($tmp);
		}
		$this->assign("levelData",$levelData);
		$this->assign("listData",$levelMsg);
		$this->display();
	}
	public function showProblemList(){
		$userinfo=session('userinfo');
		$levelId=session('levelId');
		if($_GET['id']) session('levelMsgId',$_GET['id']);
		$levelMsgId=session('levelMsgId');
		$levelMsg=M('level_msg')->where(array('id'=>$levelMsgId))->find();
		
		$problemData=M('train_problem')->where(array('level_msg_id'=>$levelMsgId,'status'=>1))->order('problem_mark')->select();
		//dump($levelMsg);
		$listIds = M('train_problem')
			->where(array('level_msg_id'=>$levelMsgId,'status'=>1))
			->getField('id',true);
		$userDo = M('train_user_problem')
			->where(array('problem_id'=>array(IN,$listIds),'user_id'=>$userinfo['id'],'level_msg_id'=>$levelMsgId,'status'=>1))
			->distinct('judge_status')
			->order('problem_id')
			->field('problem_id,judge_status')
			->select();
		//dump($userDo);
		$userDoNew = array();
        foreach($userDo as $k => $v){
			if($v['judge_status'] == 0 || $userDoNew[$v['problem_id']] > 0 
				|| $userDoNew[$v['problem_id']] == null){
				$userDoNew[$v['problem_id']] = $v['judge_status'];
			}
		}
		//dump($userDoNew);
		//dump($list);
		foreach($problemData as $k1 => $v1){
			if($userDoNew[$v1['problem_id']]==null)
				$problemData[$k1]['judge_status'] = $userDoNew[$v1['id']];
			else $problemData[$k1]['judge_status'] =$userDoNew[$v1['problem_id']];
			if(isset($problemData[$k1]['problem_id'])) $problemData[$k1]['id']=$problemData[$k1]['problem_id'];
		}
		$train_user_problem=M('train_user_problem');
		foreach($problemData as $k1 => $v1){
			$problemData[$k1]['submissions']=$train_user_problem
				->where(array('problem_id'=>$problemData[$k1]['id']))->count();
			$problemData[$k1]['accepted']=$train_user_problem
				->where(array('problem_id'=>$problemData[$k1]['id'],'judge_status'=>0))->count();
		}
		//dump($levelMsg);
		$this->assign("listData",$levelMsg);
		$this->assign("problemData",$problemData);
		$this->display();
	}
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
		$language=array('All','C++','C');
		$results=array(array());
		foreach($language as $key => $value){
			$results[$key]['index']=$value;
			$results[$key]['status']=$value;
		}
		return $results;
	}
	
	public function showTaskJudge(){
		$levelId=session('levelId');
		$levelMsgId=session('levelMsgId');
		$levelMsg=M('level_msg')->where(array('id'=>$levelId))->find();
//		$judgeData=M('train_user_problem')->where(array('level_msg_id'=>$_GET['id']))->select();
//		dump($judgeData);
		$this->assign("listData",$levelMsg);
//		$this->assign("judgeData",$judgeData);
//		$this->display();
		
		$statusArray=$this->getStatusArray();
		$languageArray=$this->getLanguage();
		//dump($statusArray);
		//dump($languageArray);
		$this->assign("statusArray",$statusArray);
		$this->assign("languageArray",$languageArray);
		$userinfo = session('userinfo');
		$problemId=$_POST['problemId'];
		$anthor=$_POST['anthor'];
		$language=$_POST['language'];
		$judgeResults=$_POST['status'];
		$parmCnt=0;
		$where['level_msg_id']=session('levelMsgId');
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
			$where['level_msg_id']=$levelMsgId;$where['problem_mark']=$problemId;
			$problemData=M('train_problem')->where($where)->find();
			$where['problem_id']  = $problemData['id'];
			$parmCnt=$parmCnt+1;
		}
		if($anthor) {
			$where['nickname']  = array('like','%'.$anthor.'%');
			$parmCnt=$parmCnt+1;
			
		}
		if($parmCnt>1) $where['_logic'] = 'and';
		//dump($where);
		$User = M('train_user_problem'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count = $User->where($where)->count();// 查询满足要求的总记录数
		$Page  = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $key => $value){
			$list[$key]['problem_mark']=
			M('train_problem')->where(array('id'=>$list[$key]['problem_id']))->find()['problem_mark'];
			
		}
		
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
	public function replace($arr){
		//dump($arr);
		$arr['sample_input']=str_replace('\n', '<br>', $arr['sample_input']);
		
		$arr['sample_output']=str_replace('\n', '<br>', $arr['sample_output']);
		
		$arr['input']=str_replace('\n', '<br>', $arr['input']);
		$arr['output']=str_replace('\n', '<br>', $arr['output']);
	
		$arr['description']=str_replace('\n', '<br>', $arr['description']);
		return $arr;
	}
	public function showProblem(){
		$lastLan=session('lastLan');
		if(!$lastLan) $lastLan="C++";
		$lanData=array("C++","C","PASCAL");
		$levelId=session('levelId');
		if(!$levelId){
			$levelId=M('train_problem')->where(array('id'=>$_GET['id']))->find()['level_msg_id'];
		}
		$levelMsgId=session('levelMsgId');
		$levelMsg=M('level_msg')->where(array('id'=>$levelId))->find();
		$problemData=M('train_problem')->where(array('id'=>$_GET['id']))->find();
		//dump($problemData);
		$train_user_problem=M('train_user_problem');
		foreach($problemData as $k => $v){
			$problemData[$k]=htmlspecialchars($problemData[$k]);
		}
		$problemData['submissions']=$train_user_problem->where(array('problem_id'=>$problemData['id']))->count();
		$tmp=$train_user_problem->where(array('problem_id'=>$problemData['id'],'judge_status'=>0))->distinct('problem_id')->select();
		//dump($tmp);
		$problemData['accepted']=count($tmp);
		//dump($problemData);
		$problemData=$this->replace($problemData);
		$this->assign("lanData",$lanData);
		$this->assign("levelMsgId",$levelMsgId);
		$this->assign("listData",$levelMsg);
		$this->assign("problemData",$problemData);
		$this->display();
	}
	public function showTaskRank(){
		
		$levelId=session('levelId');
		$levelMsgId=session('levelMsgId');
		$levelMsg=M('level_msg')->where(array('id'=>$levelId))->find();
		$this->assign("listData",$levelMsg);
		
		$userinfo=session('userinfo');
		$trainRankData=M('train_rank')
			->where(array('user_id'=>$userinfo['id'],'level_msg_id'=>$levelMsgId))
			->count();
		if(!$trainRankData){
			$record['user_id']=$userinfo['id'];
			$record['level_msg_id']=$levelMsgId;
			$record['solve_problem']=0;
			$record['submissions']=0;
			M('train_rank')->where(array('user_id'=>$userinfo['id']))->add($record);
		}
		
		$User = M('train_rank'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count      = $User->where(array('level_msg_id'=>$levelMsgId))->count();// 查询满足要求的总记录数
		$Page       = new Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User->where(array('level_msg_id'=>$levelMsgId))->order('solve_problem desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		
		foreach($list as $key => $value){
			$nickname=M('user')->where(array('id'=>$list[$key]['user_id']))->find()['nickname'];
			$list[$key]['nickname']=$nickname;
			$list[$key]['rank']=$key+1;
		}
		//dump($list);
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display(); // 输出模板;
	}
	public function isAc($arr){
		foreach($arr as $k=>$v){
			if($arr[$k]['judge_status']==0) return true;
		}
		return false;
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
	public function showRankPage(){
		$user=M('user');
		$levelId=session('levelId');
		$levelMsgId=session('levelMsgId');
		//dump($levelMsgId);
		$levelMsg=M('level_msg')->where(array('id'=>$levelId))->find();
		
		$train_user_problem=M('train_user_problem');
		$train_problem=M('train_problem');
		//dump($train_problem->select());
		$trainProblemData=$train_problem->where(array('level_msg_id'=>$levelMsgId,'status'=>1))
					->field('id,problem_mark')->order('problem_mark')->select();
		//dump($trainProblemData);
		$userIds=$train_user_problem->where(array('level_msg_id'=>$levelMsgId))
					->field('user_id')->distinct('user_id')->select();
					//dump($userIds);
		$where['level_msg_id']=$levelMsgId;
		foreach($userIds as $k1 => $v1){
			
			$user_id=$userIds[$k1]['user_id'];
			//dump($user_id);
			$rankData[$k1]['ac_number']=0;
			$rankData[$k1]['score']=0;
			$rankData[$k1]['id']=$user_id;
			foreach($trainProblemData as $k2 => $v2){
				$backgroud_color='#FFFFFF';
				$judge_status=0;
				$score=0;
				$problem_mark=$trainProblemData[$k2]['problem_mark'];
				$where['problem_id']=$trainProblemData[$k2]['id'];
				//dump($trainProblemData[$k2]['id']);
				$where['user_id']=$user_id;
				//dump($where);
				$judgeRecord=$train_user_problem->where($where)->select();
				//dump($judgeRecord);
				if($judgeRecord){//如果提交过
					$is_ac=$this->isAc($judgeRecord);
					//dump($is_ac);
					if($is_ac){
							$judge_status=2;//AC
							$backgroud_color='rgb(215,255,215)';
						$rankData[$k1]['ac_number']=$rankData[$k1]['ac_number']+1;
					}else {
						$judge_status=1;
						$backgroud_color='#FF9999';
					}
				}
				$rankData[$k1][$problem_mark]['backgroud_color']=$backgroud_color;
				$rankData[$k1][$problem_mark]['judge_status']=$judge_status;
				$rankData[$k1]['nickname']=$user->where(array('id'=>$user_id))->find()['nickname'];
			}
		}
		if($rankData)
		    $rankData = $this->sortArrByManyField($rankData,'ac_number',SORT_DESC);
		foreach($rankData as $k => $v){
			$rankData[$k]['rank']=$k+1;
		}
		//dump($trainProblemData);
		$this->assign('rankData',$rankData);
		$this->assign('trainProblemData',$trainProblemData);
		$this->display();
	}
	public function showSubmit(){
		$levelId=session('levelId');
		$levelMsgId=session('levelMsgId');
		$levelMsg=M('level_msg')->where(array('id'=>$levelId))->find();
		//dump($_GET);
		$this->assign("listData",$levelMsg);
		$this->assign("level_msg_id",$_GET['level_msg_id']);
		$this->assign("listData",$levelMsg);
		$this->assign("id",$_GET['id']);
		$this->assign("title",$_GET['title']);
		$this->display();
	}
	public function creatFile($filename){
		if(!file_exists($filename)) {
			mkdir($filename);
			chmod($filename, 0777);
		}
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
		$datapath='Data/Train/problems/'.$id;
		return $this->getfiles($datapath);
	}
	public function onlineJudge(){
		if(strstr($_POST['editor'],"freopen")){
			saveViolationMessage('代码中含有freopen');
			$this->error("代码中含有freopen，禁止提交!",U('Train/showProblemList'));
		}
		if(strstr($_POST['editor'],"fopen")){
			saveViolationMessage('代码中含有fopen');
			$this->error("代码中含有fopen，禁止提交!",U('Train/showProblemList'));
		}
		$userinfo = session('userinfo');

		$code='Data/Train/code';
		$userpath=$code.'/'.$userinfo['username'];
		$this->creatFile($code);
		$this->creatFile($userpath);
		$lan=$_POST['language'];
		if($lan=='C++'){
			$ext='.cpp';
		}else if($lan=='C'){
			$ext='.c';
		}else if($lan=='PASCAL'){
			$ext='.pas';
		}
		$condition['user_id']=$userinfo['id'];
		$condition['problem_id']=$_POST['problemID'];
		$submitSum=M('train_user_problem')->where($condition)->Count();
		$submitSum=$submitSum+1;
		
		$filepath = $userpath.'/'.$_POST['problemID'].'_'.$submitSum.$ext;
		
		$sourceCode =$_POST['editor'];
		file_put_contents($filepath, $sourceCode);
		chmod($filepath, 0777);
		
		$resultData['user_id']=$userinfo['id'];
		$resultData['problem_id']=$_POST['problemID'];
		$resultData['submit_time']=time();
		$resultData['judge_status']=8;
		$resultData['exe_time']=0;
		$resultData['exe_memory']=0;
		$resultData['code_len']=strlen($_POST['editor']);
		$resultData['language']=$_POST['language'];
		$resultData['nickname']=$userinfo['nickname'];
		$resultData['filepath']=$filepath;
		$resultData['judge_results']=0;
		$resultData['level_msg_id']=$_POST['level_msg_id'];
			
		$message=M('train_user_problem');
		$userProblemId=$message->add($resultData);
		
		//$caseNumber
		$problemData=M('train_problem')->where('id='.$_POST['problemID'])->find();
		$caseNumber=$this->getCaseNumber($_POST['problemID']);
		if($caseNumber==0){
			$this->error("此题暂无测试数据，禁止提交!",U('Train/showProblemList'));
		}
		$judgeDetail['user_problem_id']=$userProblemId;
		$judgeDetail['judge_status']=8;
		$judgeDetail['exe_time']=0;
		$judgeDetail['exe_memory']=0;
		$judgeDetail['score']=0;
		$judgeDetail['group_score']=intval(100/$caseNumber);
		for($i=1;$i<=$caseNumber;$i++){
			$caseInputPath='Data/Train/problems'.'/'.$_POST['problemID'].'/'.$i.'.in';
			$caseOutputPath='Data/Train/problems'.'/'.$_POST['problemID'].'/'.$i.'.out';
			$judgeDetail['group_id']=$i;
			$judgeDetail['input_file_path']=$caseInputPath;
			$judgeDetail['output_file_path']=$caseOutputPath;
			if($i==$caseNumber) $judgeDetail['group_score']=100-($caseNumber-1)*intval(100/$caseNumber);
			M('train_judge_detail')->add($judgeDetail);
		}
		$this->redirect('Train/showTaskJudge');
	}
	public function showSourceCode(){
	    //dump($_GET);
		$levelId=session('levelId');
		$levelMsgId=session('levelMsgId');
		$levelMsg=M('level_msg')->where(array('id'=>$levelId))->find();
		$this->assign("listData",$levelMsg);
		$data=M('train_user_problem')->where(array('id'=>$_GET['id']))->find();
		//dump($data);
		$filepath = $data['filepath'];
		$userinfo=session('userinfo');
		if($data['user_id']!=$userinfo['id'] && $userinfo['root']==0){
			saveViolationMessage('偷看源代码 运行ID'.$id);
			$this->error('不许偷看别人的源代码哦！',U("Judge/showRealTimeEvaluation"));
		}
		$contents=file_get_contents($filepath);
		$contents = htmlspecialchars($contents);
		$judgeData[0]=$data;
		$this->assign('judgeData',$judgeData);
		$this->assign('codes',$contents);
		//dump($contents);
		$this->display();
	}
	public function showJudgeDetail(){
		$levelId=session('levelId');
		$levelMsgId=session('levelMsgId');
		$levelMsg=M('level_msg')->where(array('id'=>$levelId))->find();
		$this->assign("listData",$levelMsg);
		//dump($_GET);
		$userProblemData=M('train_user_problem')
				->where(array('id'=>$_GET['id']))
				->find();
				
		$judgeData=M('train_judge_detail')
				->where(array('user_problem_id'=>$userProblemData['id']))
				->select();
		//dump($judgeData);
		$problemData=M('train_problem')
				->where(array('id'=>$userProblemData['problem_id']))
				->find();
		
		$allScore=0;
		//dump($judgeData);
		foreach($judgeData as $key => $value){
			$status=$judgeData[$key]['judge_status'];
			//dump($status);
			if($status>=0&&$status<=7){
				if($status==0) {
					//dump("TEST");
					$allScore=$allScore+$judgeData[$key]['group_score'];
					$judgeData[$key]['score']=$judgeData[$key]['group_score'];
					$data['score']=$judgeData[$key]['group_score'];
					//dump($data);
					$newScore['score']=$judgeData[$key]['group_score'];
					M('judge_detail')->where('id='.$judgeData[$key]['id'])->save($newScore);
					M('judge_detail')->where('id='.$judgeData[$key]['id'])->save($data);
				}
			}
		}
		
		//dump($problemData);
		//die;
		$userinfo = session('userinfo');
		$this->assign('score',$allScore);
		$this->assign('myRoot',$userinfo['root']);
		$this->assign('judgeData',$judgeData);
		$this->assign('userProblemData',$userProblemData);
		$this->assign('problemData',$problemData);
		$this->display();
	}
	public function getWangEditorData(){
		$levelId=session('levelId');
		$levelMsgId=session('levelMsgId');
		$userinfo=session('userinfo');
		$levelMsg=M('level_msg')->where(array('id'=>$levelId))->find();
		$this->assign("listData",$levelMsg);
		$BBSData['comment']=$_POST['html'];
		$BBSData['level_msg_id']=$levelMsgId;
		$BBSData['user_id']=$userinfo['id'];
		$BBSData['submit_time']=time();
		M('train_comment')->add($BBSData);
		$resData['url']=U('showBBS');
		$resData['status']=1;
		$this->ajaxReturn($resData, 'json');
	}
	public function showBBS(){
		$levelId=session('levelId');
		$levelMsgId=session('levelMsgId');
		$userinfo=session('userinfo');
		$levelMsg=M('level_msg')->where(array('id'=>$levelId))->find();
		$this->assign("listData",$levelMsg);
		
		
		$User = M('train_comment'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count      = $User->where(array('level_msg_id'=>$levelMsgId))->count();// 查询满足要求的总记录数
		$Page       = new Page($count,4);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$BBSData = $User->where(array('level_msg_id'=>$levelMsgId))->order('submit_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($BBSData as $key => $value){
			$BBSData[$key]['nickname']=M('user')->where(array('id'=>$userinfo['id']))->find()['nickname'];
		}
		$this->assign("BBSData",$BBSData);
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
	}
	function checkLookDataRoot($filePath){
		$userinfo=session('userinfo');
		if($userinfo['root']<2){
			saveViolationMessage('非法访问 课程数据'.$filePath);
			$this->error("非法访问，违规行为将被记录!",U('User/showLogin'));
			
		}
	}
	public function showTestData(){
		$trainJudgeData=M('train_judge_detail')->where(array('id'=>$_GET['id']))->find();
		$inFilePath=$trainJudgeData['input_file_path'];
		$outFilePath=$trainJudgeData['output_file_path'];
		if($_GET['op']==0) $testData=file_get_contents($inFilePath);
		else $testData=file_get_contents($outFilePath);
		$this->checkLookDataRoot($filePath);
		$this->assign('testData',$testData);
		$this->assign('trainJudgeData',$trainJudgeData);
		$this->display();
	}
}