<?php
// 本类由系统自动生成，仅供测试用途
class ExamAction extends BaseAction {
	public function index(){
		if(isset($_POST['value'])) $where['name']=array('like','%'.$_POST['value'].'%');
		$User = M('contest_list'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count = $User->where($where)->count();// 查询满足要求的总记录数
		$Page  = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		
		$where['is_visible']=1;
		$list = $User->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$contestLogin=session('contestLogin');
		foreach($list as $key=>$value){
			
			if(intval($value['start_time'])<time() && time()<intval($value['end_time'])){
				$list[$key]['status']="Running";
				$list[$key]['status_color']="red";
			}else if(time()<intval($value['start_time'])){
				$list[$key]['status']="Pending";
				$list[$key]['status_color']="blue";
			}else {
				$list[$key]['status']="Ended";
				$list[$key]['status_color']="gray";
			}
			if($list[$key]['type']==0){
				$list[$key]['type']='公开';
			}else if($list[$key]['type']==1){
				$list[$key]['type']='私有';
			}else if($contestLogin[$list[$key]['id']]==1){
				$list[$key]['type']='密码(已输入)';
			}
			else if($list[$key]['type']==2){
				$list[$key]['type']='密码';
			}
			$list[$key]['anthor']=M('user')->where(array('id'=>$list[$key]['user_id']))->find()['nickname'];
		}
		
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('nowTime',time());
	
		$userinfo = session('userinfo');
		$this->myId = $userinfo['id'];
		$this->display(); // 输出模板
		
	}
	public function showPending(){
		if(!isset($_GET['id'])) {
			$contestData=session('contestData');
			$_GET['id']=$contestData['id'];
		}
		$contest_list=M('contest_list')->where('id='.$_GET['id'])->select();
		if(time()>=$contest_list[0]['start_time']){
			$this->redirect('Exam/showProblemList');
		}
		$this->assign('contest_list',$contest_list[0]);// 赋值数据集
		$this->assign('nowtime',time());// 赋值数据集
		$this->display();
	}
	public function checkPassword(){
		$contestData=M('contest_list')->where(array('id'=>$_GET['id']))->find();
		if($contestData['password']==$_GET['psd']){
			$tmp=session('contestLogin');
			$tmp[$_GET['id']]=1;
			session('contestLogin',$tmp);
			$this->redirect('Exam/showProblemList',array('id' => $_GET['id']));
		}else {
			$this->error("密码错误！",U('Exam/index'));
		}
	}
	public function showProblemList(){
		$contestData=array();
		if(!isset($_GET['id'])) {
			$contestData=session('contestData');
			$_GET['id']=$contestData['id'];
			
		}else{
			$contestData=M('contest_list')->where(array('id'=>$_GET['id']))->find();
		}
		if($contestData['type']==2){
			$contestLogin=session('contestLogin');
			if($contestLogin[$_GET['id']]!=1){
				$this->error("非法进入！",U('Exam/index'));
			}
		}
		$contest_problem=M('contest_problem')->where(array('contest_id'=>$_GET['id'],'status'=>1))->select();
		$contest_list=M('contest_list')->where('id='.$_GET['id'])->select();
		if($contest_list[0]['type']==2){//需要密码
			
		}
		session('contestList',$contest_list[0]);
		session('contestData',$contest_list[0]);
		if($contest_list[0]['start_time']>time()){
			$this->redirect('Exam/showPending');
		}
		foreach($contest_list as $key=>$value){
			if(intval($value['start_time'])<time() && time()<intval($value['end_time'])){
				$contest_list[$key]['status']="正在比赛";
			}else if(time()<intval($value['start_time'])){
				$contest_list[$key]['status']="等待开始";
			}else {
				$contest_list[$key]['status']="比赛结束";
			}
		}

		$userinfo=session('userinfo');
		$userSubmitData=M('contest_user_problem')
						->where(array('contest_id'=>$contest_list[0]['id'],
									  'user_id'=>$userinfo['id']))
						->field('problem_id,judge_status')
						->select();
		//dump($userSubmitData);
		$acStatus=array();
		foreach($userSubmitData as $key => $value){
			$tmp=$userSubmitData[$key]['judge_status'];
			if($acStatus[$userSubmitData[$key]['problem_id']]==1 || $userSubmitData[$key]['judge_status']==0){
				$acStatus[$userSubmitData[$key]['problem_id']]=1;
			}else if($tmp!=8&&$tmp!=9&&$tmp!=10){
				$acStatus[$userSubmitData[$key]['problem_id']]=-1;
			}
		}
		//dump($acStatus);
		//dump($contest_problem);
		$problem=M('contest_problem');
		$contestUserProblem=M('contest_user_problem');
		foreach($contest_problem as $key => $value){
			$contest_problem[$key]['accepted']
				=$contestUserProblem
				->where(array('contest_id'=>$_GET['id'],'judge_status'=>0,'problem_id'=>$contest_problem[$key]['id']))
				->count();
			$contest_problem[$key]['submissions']
				=$contestUserProblem
				->where(array('contest_id'=>$_GET['id'],'problem_id'=>$contest_problem[$key]['id']))
				->count();
			if($acStatus[$contest_problem[$key]['id']]==1){
				$contest_problem[$key]['acStatus']=1;
			}else if($acStatus[$contest_problem[$key]['id']]==-1){
				$contest_problem[$key]['acStatus']=-1;
			}else {
				$contest_problem[$key]['acStatus']=0;
			}
		}
		
		$this->assign('contest_list',$contest_list[0]);// 赋值数据集
		$this->assign('list',$contest_problem);// 赋值数据集
		$this->display(); // 输出模板
	}
	/*显示问题*/
	public function showProblem(){
		$problemData=M('contest_problem')->where('id='.$_GET['id'])->find();
		$lastLan=session('lastLan');
		if(!$lastLan) $lastLan="C++";
		$lanData=array("C++","C","PASCAL");
		$this->assign('lanData',$lanData);
		$this->assign('lastLan',$lastLan);
		$this->assign('problemData',$problemData);
		$this->display(); // 输出模板
	}
	public function getStatusArray(){
		$status=array('Accepted','Wrong Answer','Time Limit Exceeded',
		'Memory Limit Exceeded','Runtime Error','Compilation Error',
		'Output Limit Exceeded','Input Limit Exceeded','Pending',
		'Compiling','Running','All');
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
	public function showRealTimeEvaluation(){
		$contestData=session('contestData');
		$statusArray=$this->getStatusArray();
		$languageArray=$this->getLanguage();
		$this->assign("statusArray",$statusArray);
		$this->assign("languageArray",$languageArray);
		$userinfo = session('userinfo');
		$problemId=$this->getValue($_POST,$_GET,'problemId');
		$anthor=$this->getValue($_POST,$_GET,'anthor');
		$language=$this->getValue($_POST,$_GET,'language');
		$judgeResults=$this->getValue($_POST,$_GET,'status');
		$parmCnt=1;
		$where['contest_id']=$contestData['id'];
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
			$where['problem_id']  = M('contest_problem')->where(array('contest_id'=>$contestData['id'],'problem_mark'=>$problemId))->find()['id'];
			$parmCnt=$parmCnt+1;
		}
		if($anthor) {
			$where['nickname']  = array('like','%'.$anthor.'%');
			$parmCnt=$parmCnt+1;
		}
		if($parmCnt>1) $where['_logic'] = 'and';
		//dump($where);
		$User = M('contest_user_problem'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count = $User->where($where)->count();// 查询满足要求的总记录数
		$Page  = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		if(!$language) $language="All";
		if(!isset($judgeResults)) $judgeResults=11;
		//dump($judgeResults);
		//dump($show);
		if(!isset($_GET['p'])) $pageNumber=0;
		else $pageNumber=$_GET['p']-1;
		$allNumber=count($list);
		foreach($list as $k => $v){
			$list[$k]['problem_mark']=M('contest_problem')->where(array('id'=>$list[$k]['problem_id']))->find()['problem_mark'];
			$list[$k]['runId']=$count-($k+1+$pageNumber*15)+1;
		}
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
		$datapath='Data/Contest/problems/'.$id;
		
		return $this->getfiles($datapath);
	}
	public function onlineJudge(){
		$contestData=session('contestData');
		$userinfo=session('userinfo');
		if(strstr($_POST['editor'],"freopen")){
			saveViolationMessage('代码中含有freopen');
			$this->error("代码中含有freopen，禁止提交!",U('Exam/showProblemList'));
		}
		if(strstr($_POST['editor'],"fopen")){
			saveViolationMessage('代码中含有fopen');
			$this->error("代码中含有fopen，禁止提交!",U('Exam/showProblemList'));
		}
		if(!$userinfo){
			$this->error("别调皮啦，提交代码前，请先登录!",U('User/showLogin'));
		}
		$problemData=M('contest_problem')->where('id='.$_POST['problemID'])->find();
		if(!$problemData){
			$this->error('题目不存在!',U('Exam/showProblemList',array('id'=>$contestData['id'])));
		}
		if(time()>$contestData['end_time']){
			$this->error("比赛已经结束，禁止提交！",U('Exam/showProblemList',array('id'=>$contestData['id'])));
		}
		$caseNumber=$this->getCaseNumber($_POST['problemID']);
		if($caseNumber==0){
			$this->error("此题暂无测试数据，禁止提交!",U('Exam/showProblemList'));
		}
		$timeLimit=intval($problemData['time_limit'],10);
		$memoryLimit=intval($problemData['memory_limit'],10);
		$code='Data/Contest/code';
		if(!file_exists($code)) {
			mkdir($code);
			chmod($code, 0777);
		}
		if(!file_exists($code.'/'.$userinfo['username'])) {
			mkdir($code.'/'.$userinfo['username']);
			chmod($code.'/'.$userinfo['username'], 0777);
		}
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
		$submitSum=M('contest_user_problem')->where($condition)->Count();
		$submitSum=$submitSum+1;
		$filename = $code.'/'.$userinfo['username'].'/'.$_POST['problemID'].'_'.$submitSum.$ext;
		$word =$_POST['editor'];
		
		file_put_contents($filename, $word);
		chmod($filename, 0777);
		$destFile=$filename;
		$file=$_POST['problemID'];
		$verdict=8;//设置成待判状态

		$resultData['user_id']=$userinfo['id'];
		$resultData['problem_id']=$_POST['problemID'];
		$resultData['contest_id']=M('contest_problem')->where(array('id'=>$_POST['problemID']))->find()['contest_id'];
		$resultData['submit_time']=time();
		$resultData['judge_status']=$verdict;
		$resultData['exe_time']=0;
		$resultData['exe_memory']=0;
		$resultData['code_len']=strlen($_POST['editor']);
		$resultData['language']=$_POST['language'];
		$resultData['nickname']=$userinfo['nickname'];
		$resultData['filepath']=$filename;
		$resultData['score']=0;
		$message=M('contest_user_problem');
		$userProblemId=$message->add($resultData);
		
		//$caseNumber
		$judgeDetail['user_problem_id']=$userProblemId;
		$judgeDetail['judge_status']=8;
		$judgeDetail['exe_time']=0;
		$judgeDetail['exe_memory']=0;
		$judgeDetail['score']=0;
		$judgeDetail['group_score']=intval(100/$caseNumber);
		for($i=1;$i<=$caseNumber;$i++){
			$caseInputPath='Data/Contest/problems'.'/'.$_POST['problemID'].'/'.$i.'.in';
			$caseOutputPath='Data/Contest/problems'.'/'.$_POST['problemID'].'/'.$i.'.out';
			$judgeDetail['group_id']=$i;
			$judgeDetail['input_file_path']=$caseInputPath;
			$judgeDetail['output_file_path']=$caseOutputPath;
			if($i==$caseNumber) $judgeDetail['group_score']=100-($caseNumber-1)*intval(100/$caseNumber);
			M('contest_judge_detail')->add($judgeDetail);
		}
		$this->redirect('Exam/showRealTimeEvaluation');
	}
	public function showUserCode(){
		$id=$_GET['id'];
		$condition['id']=$id;
		$userinfo=session('userinfo');
		$coder=M('contest_user_problem')->where(array("id"=>$_GET['id']))->find()['user_id'];
		if($userinfo['root']==0&&$userinfo['id']!=$coder){
			$this->error('不许偷看别人的源代码哦！',U("Exam/showRealTimeEvaluation"));
		}
		$data[0]=M('contest_user_problem')->where($condition)->find();
		//dump($data);
		$filename = $data[0]['filepath'];

		$contents=file_get_contents($filename);
		$contents = htmlspecialchars($contents);
		$this->assign('code',$contents);
		$this->assign('judgeData',$data);
		$this->display();
	}
	public function showJudgeDetail(){
		$contestData=session('contestData');
		$userProblemData=M('contest_user_problem')
				->where(array('id'=>$_GET['id']))
				->find();
		$judgeData=M('contest_judge_detail')
				->where(array('user_problem_id'=>$userProblemData['id']))
				->select();
		//dump($judgeData);
		$problemData=M('contest_problem')
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
					M('contest_judge_detail')->where('id='.$judgeData[$key]['id'])->save($newScore);
					M('contest_judge_detail')->where('id='.$judgeData[$key]['id'])->save($data);
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
	public function sort3Fields(&$data, $filed1, $type1, $filed2, $type2, $filed3, $type3)
	{
	    if (count($data) <= 0) {
	        return $data;
	    }
	    foreach ($data as $key => $value) {
	        $sort_filed1[$key] = $value[$filed1];
	        $sort_filed2[$key] = $value[$filed2];
	        $sort_filed3[$key] = $value[$filed3];
	    }
	    array_multisort($sort_filed1, $type1, $sort_filed2, $type2, $sort_filed3, $type3, $data);
	    return $data;
	}
	public  function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){  
        if(is_array($arrays)){  
            foreach ($arrays as $array){  
                if(is_array($array)){  
                    $key_arrays[] = $array[$sort_key];  
                }else{  
                    return false;  
                }  
            }  
        }else{  
            return false;  
        } 
        array_multisort($key_arrays,$sort_order,$sort_type,$arrays);  
        return $arrays;  
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
    public function getProblemScore($id,$score){
    	//dump($id);
    	//dump($score);
    	$all=M('contest_judge_detail')->where(array('user_problem_id'=>$id))->count();
    	$ac=M('contest_judge_detail')->where(array('user_problem_id'=>$id,'judge_status'=>0))->count();
    	return $score*$ac/$all;
    }
	public function showRankPage(){
		$contestData=session('contestData');
		$where['contest_id']=$contestData['id'];
		$where['judge_status']=array('not in','8,9,10');
		
		$contest_user_problem=M('contest_user_problem');
		$judgeRecord=$contest_user_problem->where($where)->order('id')->select();
		if(!$judgeRecord) return $this->display();
		$ProblemMark=M('contest_problem')
			->where(array('contest_id'=>$contestData['id']))
			->distinct('problem_mark')
			->field('problem_mark,id,score')
			->select();
		//dump($ProblemMark);
		$userData=M('user')
			->field('nickname,id')
			->select();
		$userIdToNickname=array();
		foreach($userData as $k => $v){
			$userIdToNickname[$userData[$k]['id']]=$userData[$k]['nickname'];
		}
		//dump($userIdToNickname);
		$problemIdToMark=array();
		$problem=array();
		foreach($ProblemMark as $k => $v){
			$problemIdToMark[$ProblemMark[$k]['id']]=$ProblemMark[$k]['problem_mark'];
			$problem[$k]=$ProblemMark[$k]['problem_mark'];
		}
		//dump($problemIdToMark);
		//dump($ProblemMark);
		$cnt=0;
		$map=array();
		$firstAc=array();
		foreach($judgeRecord as $k => $v){
			if(!isset($map[$judgeRecord[$k]['user_id']])){
				$map[$judgeRecord[$k]['user_id']]=$cnt;
				$sort[$cnt]['user_id']=$judgeRecord[$k]['user_id'];
				$sort[$cnt]['nickname']=$userIdToNickname[$judgeRecord[$k]['user_id']];
				
				foreach($ProblemMark as $k1 => $v1){
					$sort[$cnt][$ProblemMark[$k1]['problem_mark']]['ac']=0;
					$sort[$cnt][$ProblemMark[$k1]['problem_mark']]['score']=0;
				}
				$cnt=$cnt+1;
				//dump("哈哈哈");
			}
			$tmp=$map[$judgeRecord[$k]['user_id']];
			//dump($tmp);
			if(!isset($sort[$tmp]['ac_number'])) $sort[$tmp]['ac_number']=0;
			if(!isset($sort[$tmp]['score'])) $sort[$tmp]['score']=0;
			if(!isset($sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['wa']))
				$sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['wa']=0;
			if(!isset($sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['score']))
				$sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['score']=0;
			if($judgeRecord[$k]['judge_status']==0){
				//dump($judgeRecord[$k]);
				//dump($tmp);
				$con=array();
				$con['user_id']=$judgeRecord[$k]['user_id'];
				$con['problem_id']=$judgeRecord[$k]['problem_id'];
				$con['contest_id']=$contestData['id'];
				$con['judge_status']=array('not in','0,8,9,10');
				$con['id']=array('lt',$judgeRecord[$k]['id']);
				$notAcNum=$contest_user_problem->where($con)->count();
				$sort[$tmp]['all_time']+=intval((intval($judgeRecord[$k]['submit_time'])-intval($contestData['start_time']))/60);
				$sort[$tmp]['all_time']+=$notAcNum*20;
				//dump($sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['all_time']);
				//dump($problemIdToMark[$judgeRecord[$k]['problem_id']]);
				$acres=$sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['ac'];
				if($acres<1)
					$sort[$tmp]['ac_number']=$sort[$tmp]['ac_number']+1;
				$sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['ac']=1;
				$problemMark=$problemIdToMark[$judgeRecord[$k]['problem_id']];
				$problemScore=M('contest_problem')
					->where(array('contest_id'=>$contestData['id'],'problem_mark'=>$problemMark))
					->find()['score'];
				$sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['score']=$problemScore;
					
				if(!isset($firstAc[$problemIdToMark[$judgeRecord[$k]['problem_id']]])){
					$firstAc[$problemIdToMark[$judgeRecord[$k]['problem_id']]]=1;
					$sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['ac']=2;
				}
				
			}else if($sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['ac']>0){
				continue;
			}
			else{
				$sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['ac']=-1;
				$problemMark=$problemIdToMark[$judgeRecord[$k]['problem_id']];
				$problemScore=M('contest_problem')
					->where(array('contest_id'=>$contestData['id'],'problem_mark'=>$problemMark))
					->find()['score'];
				$sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['score']=
				max($sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['score'],
				$this->getProblemScore($judgeRecord[$k]['id'],$problemScore));
				$sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['wa']-=1;
			}
			//$sort[$tmp]['score']=$sort[$tmp]['score']+$sort[$tmp][$problemIdToMark[$judgeRecord[$k]['problem_id']]]['score'];
		}
		
		for($i=0;$i<$cnt;$i++){
			foreach($ProblemMark as $k => $v){
				$mark=$ProblemMark[$k]['problem_mark'];
				if(isset($sort[$tmp][$mark]['score']))
					$sort[$i]['score']+=$sort[$i][$mark]['score'];
			}
		}
		//dump($sort);
		//$sorted=$this->my_sort($sort,'score',SORT_DESC);
		$sorted=$this->sortArrByManyField($sort,'score',SORT_DESC,'ac_number',SORT_DESC,'all_time',SORT_ASC);

		//$sorted=$this->sort3Fields($sort,['score'=>SORT_DESC,'ac_number'=>SORT_DESC,'all_time'=>SORT_ASC]);
		$cnt=0;
		foreach($sorted as $k => $v){
			$cnt=$cnt+1;
			if($k==0){
				$sorted[$k]['rank']=$cnt;
				continue;
			}
//			if($sorted[$k]['score']==$sorted[$k-1]['score']){
//				$sorted[$k]['rank']=$sorted[$k-1]['rank'];
//			}
			else {
				$sorted[$k]['rank']=$cnt;
			}
		}
		//dump($sorted);
		$this->assign('ProblemMark',$ProblemMark);
		$this->assign('sorted',$sorted);
		$this->assign('problem',$problem);
		$this->display();
	}
	 function checkLookDataRoot($filePath){
		$userinfo=session('userinfo');
		if($userinfo['root']<2){
			saveViolationMessage('非法访问 比赛数据'.$filePath);
			$this->error("非法访问，违规行为将被记录!",U('User/showLogin'));
			
		}
	}
    public function showTestData(){
        //dump($_GET);
        
        $data=M('contest_judge_detail')->where(array('id'=>$_GET['id']))->find();
        if($_GET['op']) $filePath=$data['output_file_path'];
        else $filePath=$data['input_file_path'];
        //dump($filePath);
        $this->checkLookDataRoot($filePath);
        $contents=file_get_contents($filePath);
        $contents = htmlspecialchars($contents);
        $this->assign('text',$contents);
        $this->display();
    }
}