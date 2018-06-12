<?php
// 本类由系统自动生成，仅供测试用途
class LadderAction extends BaseAction {
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
		if(!isset($_GET['id'])){
			$contestData=session('contestData');
			$_GET['id']=$contestData['id'];
		}
   }
	public function showContestListPage(){
		if($_POST['value']) $where['name']=array('like','%'.$_POST['value'].'%');
		$where['is_visible']=0;
		$where['_logic']='AND';
		$contestListData=M('ladder_contest')->order('creat_time desc')->where($where)->select();
		$ladder_user_problem=M('ladder_user_problem');
		$ladder_contest_problem=M('ladder_contest_problem');
		foreach($contestListData as $k => $v){
			$where['contest_id']=$contestListData[$k]['id'];
			$contestListData[$k]['join_number']=count($ladder_user_problem->where($where)
						->distinct(true)->field('user_id')->select());
			$contestProblemData=$ladder_contest_problem->where(array('contest_id'=>$contestListData[$k]['id']))->order('problem_id')->select();
			$userinfo=session('userinfo');
			$where1['user_id']=$userinfo['id'];
			$where1['contest_id']=$contestListData[$k]['id'];
			$score=0;
			foreach($contestProblemData as $k1 => $v1){
				$mark=$contestProblemData[$k1]['problem_mark'];
				$contestListData[$k]['problems'][$k1]=$mark;
				$where1['problem_id']=$contestProblemData[$k1]['problem_id'];
				//dump($where1);
				$res=$ladder_user_problem->where($where1)->find();
				//dump($res);
				$contestListData[$k][$mark]['problem_id']=$contestProblemData[$k1]['problem_id'];
				if(!$res){
					$contestListData[$k][$mark]['judge_status']=-1;
					$contestListData[$k][$mark]['color']='rgb(215,255,215)';
					$contestListData[$k][$mark]['score']=0;
					
				}else{
					if($res['judge_status']==0){
						$contestListData[$k][$mark]['judge_status']=0;
						
					}else {
						$contestListData[$k][$mark]['judge_status']=1;
					}
					$contestListData[$k][$mark]['color']='#E8F2FF';
					$contestListData[$k][$mark]['score']=$res['score'];
					$score=$score+$res['score'];
				}
			}
			$contestListData[$k]['score']=$score;
		}
		$tips[0]['tip']='未提交';$tips[0]['color']='#CCFFFF';
		$tips[1]['tip']='已提交';$tips[1]['color']='#C0C0C0';
		//dump($contestListData);
		$this->assign('tips',$tips);
		$this->assign('contestListData',$contestListData);
		$this->display();
	}
	public function showProblemListPage(){
		$contestData=M('ladder_contest')->where(array('id'=>$_GET['id']))->find();
		session('contestData',$contestData);
		$problemModel=M('problem');
		$contestProblemData=M('ladder_contest_problem')->where(array('contest_id'=>$contestData['id']))->order('problem_mark')->select();
		$ladder_user_problem=M('ladder_user_problem');
		$userinfo=session('userinfo');
		$where['user_id']=$userinfo['id'];
		$where['contest_id']=$contestData['id'];
		foreach($contestProblemData as $k => $v){
			$contestProblemData[$k]['title']=$problemModel->where(array('id'=>$contestProblemData[$k]['problem_id']))->find()['title'];
			$judge_status=-1;
			$where['problem_id']=$contestProblemData[$k]['problem_id'];
			$res=$ladder_user_problem->where($where)->find();
			if($res){
				if($res['judge_status']==0) $judge_status=0;
				else $judge_status=1;
			}
			$contestProblemData[$k]['judge_status']=$judge_status;
		}
		$this->assign('contestData',$contestData);
		$this->assign('contestProblemData',$contestProblemData);
		$this->display();
	}
	protected function setContestData($problemId){
        $problemData=M('ladder_contest_problem')->where(array('problem_id'=>$problemId))->find();
        $contestData=M('ladder_contest')->where(array('id'=>$problemData['contest_id']))->find();
        session('contestData',$contestData);
    }
	public function showProblemPage(){
        $this->setContestData($_GET['id']);
		$contestData=session('contestData');
		$userinfo=session('userinfo');
		$where['contest_id']=$contestData['id'];
		$where['problem_id']=$_GET['id'];
		$where['user_id']=$userinfo['id'];
		$res=M('ladder_user_problem')->where($where)->find();
		$submit_ok=1;
		if($res) $submit_ok=0;
		$contestProblemData=M('ladder_contest_problem')->where(array('problem_id'=>$_GET['id']))->find();
		$problemId=$_GET['id'];
		$arr=M('problem')->find($problemId);
		if(!$arr){
			$this->error('不要调皮，乱输入题号！',U("Index/index"));
		}
		$lastLan=session('lastLan');
		if(!$lastLan) $lastLan="C++";
		$lanData=array("C++","C","PASCAL");
		$this->assign('contestProblemData',$contestProblemData);
		$this->assign('lanData',$lanData);
		$this->assign('lastLan',$lastLan);
		$this->assign('problemData',$arr);
		$this->assign('submit_ok',$submit_ok);
		$this->display();
	}
	public function submitProblem($problemId,$usercode){
        $contestData=session('contestData');
        $userinfo=session('userinfo');
        $where['contest_id']=$contestData['id'];
        $where['problem_id']=$problemId;
        $where['user_id']=$userinfo['id'];


        $problemData=M('problem')->where('id='.$problemId)->find();

        $caseNumber=$problemData['case_number'];
        $code='Data/Library/code';
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
        $condition['problem_id']=$problemId;
        $submitSum=M('user_problem')->where($condition)->Count();
        $submitSum=$submitSum+1;
        //dump($submitSum);

        $filename = $code.'/'.$userinfo['username'].'/'.$_POST['problemID'].'_'.$submitSum.$ext;
        $word =$usercode;
        file_put_contents($filename, $word);
        chmod($filename, 0777);


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

        $ladderProblem=M('ladder_contest_problem')->where(array('problem_id'=>$_POST['problemID']))->find();

        $allscore=$ladderProblem['score'];

        $message=M('user_problem');
        $userProblemId=$message->add($resultData);

        //$caseNumber
        $judgeDetail['user_problem_id']=$userProblemId;
        $judgeDetail['judge_status']=8;
        $judgeDetail['exe_time']=0;
        $judgeDetail['exe_memory']=0;
        $judgeDetail['score']=0;
        $judgeDetail['group_score']=intval($allscore/$caseNumber);
        for($i=1;$i<=$caseNumber;$i++){
            $caseInputPath='Data/Library/problems'.'/'.$_POST['problemID'].'/'.$i.'.in';
            $caseOutputPath='Data/Library/problems'.'/'.$_POST['problemID'].'/'.$i.'.out';
            $judgeDetail['group_id']=$i;
            $judgeDetail['input_file_path']=$caseInputPath;
            $judgeDetail['output_file_path']=$caseOutputPath;
            if($i==$caseNumber) $judgeDetail['group_score']=$allscore-($caseNumber-1)*intval($allscore/$caseNumber);
            M('judge_detail')->add($judgeDetail);
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
		$datapath='Data/Library/problems/'.$id;
		return $this->getfiles($datapath);
	}
	public function onlineJudge(){
        $contestData=session('contestData');
        $userinfo=session('userinfo');
        $where['contest_id']=$contestData['id'];
        $where['problem_id']=$_POST['problemID'];
        $where['user_id']=$userinfo['id'];
        $res=M('ladder_user_problem')->where($where)->find();
        $submit_ok=1;
        if($res) $submit_ok=0;
		if($submit_ok==0) $this->error("别调皮啦!",U('Ladder/showProblemListPage'));
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
		$problemData=M('problem')->where('id='.$_POST['problemID'])->find();
		if(!$problemData){
			$this->error('非法操作!!!!!!!');
		}
		$caseNumber=$this->getCaseNumber($_POST['problemID']);
		if($caseNumber==0){
			$this->error("此题暂无测试数据，禁止提交!",U('Ladder/showProblemList'));
		}
		$code='Data/Ladder/code';
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
		$submitSum=M('ladder_user_problem')->where($condition)->Count();
		$submitSum=$submitSum+1;
		//dump($submitSum);
		
		$filename = $code.'/'.$userinfo['username'].'/'.$_POST['problemID'].'_'.$submitSum.$ext;
		$word =$_POST['editor'];
		$this->submitProblem($_POST['problemID'],$word);
		file_put_contents($filename, $word);
		chmod($filename, 0777);

		$destFile=$filename;
		$file=$_POST['problemID'];
	
		
		$verdict=8;//设置成待判状态

		$resultData['user_id']=$userinfo['id'];
		$resultData['problem_id']=$_POST['problemID'];
		$resultData['contest_id']=$contestData['id'];
		$resultData['submit_time']=time();
		$resultData['judge_status']=$verdict;
		$resultData['exe_time']=0;
		$resultData['exe_memory']=0;
		$resultData['code_len']=strlen($_POST['editor']);
		$resultData['language']=$_POST['language'];
		$resultData['nickname']=$userinfo['nickname'];
		$resultData['filepath']=$filename;
		//dump($resultData);
		
		$ladderProblem=M('ladder_contest_problem')->where(array('problem_id'=>$_POST['problemID']))->find();

		$allscore=$ladderProblem['score'];

		$message=M('ladder_user_problem');
		$userProblemId=$message->add($resultData);
		
		//$caseNumber
		$judgeDetail['user_problem_id']=$userProblemId;
		$judgeDetail['judge_status']=8;
		$judgeDetail['exe_time']=0;
		$judgeDetail['exe_memory']=0;
		$judgeDetail['score']=0;
		$judgeDetail['group_score']=intval($allscore/$caseNumber);
		for($i=1;$i<=$caseNumber;$i++){
			$caseInputPath='Data/Library/problems'.'/'.$_POST['problemID'].'/'.$i.'.in';
			$caseOutputPath='Data/Library/problems'.'/'.$_POST['problemID'].'/'.$i.'.out';
			$judgeDetail['group_id']=$i;
			$judgeDetail['input_file_path']=$caseInputPath;
			$judgeDetail['output_file_path']=$caseOutputPath;
			if($i==$caseNumber) $judgeDetail['group_score']=$allscore-($caseNumber-1)*intval($allscore/$caseNumber);
			M('ladder_judge_detail')->add($judgeDetail);
		}
		$this->redirect('Ladder/showRealTimeEvaluation');
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
		$contset_problem=M('ladder_contest_problem');
		$statusArray=$this->getStatusArray();
		$languageArray=$this->getLanguage();
		$this->assign("statusArray",$statusArray);
		$this->assign("languageArray",$languageArray);
		$userinfo = session('userinfo');
		$problemMark=$this->getValue($_POST,$_GET,'problem_mark');
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
		if($problemMark) {
			$where['problem_id']  =$contset_problem->where(array('contest_id'=>$contestData['id'],'problem_mark'=>$problemMark))->find()['problem_id'];
			$parmCnt=$parmCnt+1;
		}
		if($anthor) {
			$where['nickname']  = array('like','%'.$anthor.'%');
			$parmCnt=$parmCnt+1;
		}
		if($parmCnt>1) $where['_logic'] = 'and';
		//dump($where);
		$User = M('ladder_user_problem'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count = $User->where($where)->count();// 查询满足要求的总记录数
		$Page  = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		foreach($list as $k => $v){
			$list[$k]['problem_mark']=$contset_problem->where(array('problem_id'=>$list[$k]['problem_id']))->find()['problem_mark'];
		}
		if(!$language) $language="All";
		//dump($language);
		if(!isset($judgeResults)) $judgeResults=11;
		//dump($judgeResults);
		$this->assign("lan",$language);
		$this->assign("sta",$judgeResults);
		$this->assign("problem_mark",$problemMark);
		$this->assign("ant",$anthor);
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$userinfo = session('userinfo');
		$this->myId = $userinfo['id'];
		$this->myRoot=$userinfo['root'];
		$this->display(); // 输出模板
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
		$contestData=session('contestData');
		$ladder_user_problem=M('ladder_user_problem');
		$ladder_contest_problem=M('ladder_contest_problem');
		$ladderProblemData=$ladder_contest_problem->where(array('contest_id'=>$contestData['id']))
					->field('problem_id,problem_mark')->order('problem_mark')->select();
		
		$userIds=$ladder_user_problem->where(array('contest_id'=>$contestData['id']))
					->field('user_id')->distinct('user_id')->select();
		$where['contest_id']=$contestData['id'];
		$prblemAc=array();
		foreach($userIds as $k1 => $v1){
			$user_id=$userIds[$k1]['user_id'];
			$rankData[$k1]['ac_number']=0;
			$rankData[$k1]['score']=0;
			$rankData[$k1]['id']=$user_id;
			foreach($ladderProblemData as $k2 => $v2){
				$backgroud_color='#FFFFFF';
				$judge_status=0;
				$score=0;
				$problem_mark=$ladderProblemData[$k2]['problem_mark'];
				$where['problem_id']=$ladderProblemData[$k2]['problem_id'];
				$where['user_id']=$user_id;
				$judgeRecord=$ladder_user_problem->where($where)->find();
				if($judgeRecord){//如果提交过
					$score=$judgeRecord['score'];
					if($judgeRecord['judge_status']==0){
						$where1['id']=array('lt',$judgeRecord['id']);
						$where1['problem_id']=$judgeRecord['problem_id'];
						$where1['judge_status']=0;
						$res=$ladder_user_problem->where($where1)->find();
						if($res){
							$judge_status=2;
							$backgroud_color='rgb(215,255,215)';
						}
						else {
							$prblemAc['$problem_mark']=1;
							$judge_status=3;
							$backgroud_color='rgb(215,255,215)';
						}
						$rankData[$k1]['ac_number']=$rankData[$k1]['ac_number']+1;
					}else {
						$judge_status=1;
						$backgroud_color='#FF9999';
					}
				}
				$rankData[$k1][$problem_mark]['backgroud_color']=$backgroud_color;
				$rankData[$k1][$problem_mark]['judge_status']=$judge_status;
				$rankData[$k1][$problem_mark]['score']=$score;
				$rankData[$k1]['score']=$rankData[$k1]['score']+$score;
				$rankData[$k1]['nickname']=$user->where(array('id'=>$user_id))->find()['nickname'];
			}
		}
		if($rankData)
		    $rankData = $this->sortArrByManyField($rankData,'score',SORT_DESC);
		foreach($rankData as $k => $v){
			$rankData[$k]['rank']=$k+1;
		}
		//dump($rankData);
		$this->assign('rankData',$rankData);
		$this->assign('ladderProblemData',$ladderProblemData);
		$this->display();
	}
    public function showJudgeDetail(){
        $userProblemData=M('ladder_user_problem')
            ->where(array('id'=>$_GET['id']))
            ->find();
        $judgeData=M('ladder_judge_detail')
            ->where(array('user_problem_id'=>$userProblemData['id']))
            ->select();
        //dump($judgeData);
        $problemData=M('problem')
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
                    M('ladder_judge_detail')->where('id='.$judgeData[$key]['id'])->save($newScore);
                    M('ladder_judge_detail')->where('id='.$judgeData[$key]['id'])->save($data);
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
    public function showUserCode(){
        $id=$_GET['id'];
        $condition['id']=$id;
        $userinfo=session('userinfo');
        if($userinfo['root']==0&&$userinfo['id']!=$_GET['userId']){
        	saveViolationMessage('偷看源代码 运行ID'.$id);
            $this->error('不许偷看别人的源代码哦！',U("Ladder/showRealTimeEvaluation"));
        }
        $data[0]=M('ladder_user_problem')->where($condition)->find();
        //dump($data);
        $filename = $data[0]['filepath'];
		if($data[0]['user_id']!=$_GET['userId'] && $userinfo['root']==0){
			saveViolationMessage('偷看源代码 运行ID'.$id);
			$this->error('不许偷看别人的源代码哦！',U("Ladder/showRealTimeEvaluation"));
		}
        $contents=file_get_contents($filename);
        $contents = htmlspecialchars($contents);
        $this->assign('code',$contents);
        $this->assign('judgeData',$data);
        $this->display();
    }
    function checkLookDataRoot($filePath){
		$userinfo=session('userinfo');
		if($userinfo['root']<2){
			saveViolationMessage('非法访问 天梯赛数据'.$filePath);
			$this->error("非法访问，违规行为将被记录!",U('User/showLogin'));
			
		}
	}
    public function showTestData(){
        //dump($_GET);
        $data=M('ladder_judge_detail')->where(array('id'=>$_GET['id']))->find();
        if($_GET['op']) $filePath=$data['output_file_path'];
        else $filePath=$data['input_file_path'];
        $this->checkLookDataRoot($filePath);
        $contents=file_get_contents($filePath);
        $contents = htmlspecialchars($contents);
        $this->assign('text',$contents);
        $this->display();
    }
}