<?php
// 本类由系统自动生成，仅供测试用途
class TestAction extends BaseAction {
	
	public function index(){
		$this->display();
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
    public function showProblemList(){
		$userinfo = session('userinfo');
		$value=$_POST['value'];
		$where['title']  = array('like','%'.$value.'%');
		$where['id']  = array('like','%'.$value.'%');
		$where['description']  = array('like','%'.$value.'%');
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
		$map['status']=1;
		$User = M('test_problem'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count = $User->where($map)->count();// 查询满足要求的总记录数
		$Page  = new Page($count,50);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
	//	dump($Page);
		$listIds = $User
		->where($map)
		->limit($Page->firstRow.','.$Page->listRows)
		->getField('id',true);

		$userDo = M('test_user_problem')
			->where(array('problem_id'=>array(IN,$listIds),'user_id'=>$userinfo['id']))
			->distinct('judge_status')
			->order('problem_id')
			->field('problem_id,judge_status')
			->select();

		$userDoNew = array();
        foreach($userDo as $k => $v){
			if($v['judge_status'] == 0 || $userDoNew[$v['problem_id']] > 0 
				|| $userDoNew[$v['problem_id']] == null){
				$userDoNew[$v['problem_id']] = $v['judge_status'];
			}
		}
		$test_user_problem=M('test_user_problem');
		foreach($list as $k1 => $v1){
			if($problemMark[$list[$k1]['id']]){
				unset($list[$k1]);
				continue;
			}
			if($userDoNew[$v1['problem_id']]==null){
				$list[$k1]['judge_status'] = $userDoNew[$v1['id']];
			}
			else {
				$list[$k1]['judge_status'] =$userDoNew[$v1['problem_id']];
			}
			$problem_id=$list[$k1]['id'];
			$list[$k1]['accepted']=$test_user_problem->where(array('problem_id'=>$problem_id,'judge_status'=>0))->count();
			$list[$k1]['submissions']=$test_user_problem->where(array('problem_id'=>$problem_id))->count();
			if(isset($list[$k1]['judge_status'])){
				if($list[$k1]['judge_status']==0) $list[$k1]['class_status']="success";
				else $list[$k1]['class_status']="active";
			}else {
				$list[$k1]['class_status']="active";
			}
			if(isset($list[$k1]['problem_id'])) {
				$list[$k1]['id']=$list[$k1]['problem_id'];
				//$list[$k1]['class_status']="active";
			}
		}
		
		$this->assign('problemData',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->display();
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
		$problemId=$_GET['id'];
		$userinfo=session('userinfo');
		//echo "problemId".$problemId;
		$arr=M('test_problem')->find($problemId);
		$ans=M('ladder_contest_problem')->where(array('problem_id'=>$problemId))->find();
		if($ans){
			$record=M('ladder_user_problem')->where(array('user_id'=>$userinfo['id'],'problem_id'=>$problemId))->find();
			if(!$record){
				$this->error('不要调皮，乱输入题号！',U("Test/showProblemList"));
			}
		}
		if(!$arr){
			$this->error('不要调皮，乱输入题号！',U("Test/showProblemList"));
		}
		$lastLan=session('lastLan');
		if(!$lastLan) $lastLan="C++";
		$lanData=array("C++","C","PASCAL");
		$this->assign('lanData',$lanData);
		$this->assign('lastLan',$lastLan);
		$this->assign('problemData',$arr);
		$this->display();
	}
	/*获取所有状态*/
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
	/*获取所有语言*/
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
	public function showMySubmit(){
		$statusArray=$this->getStatusArray();
		$languageArray=$this->getLanguage();
		$this->assign("statusArray",$statusArray);
		$this->assign("languageArray",$languageArray);
		$userinfo = session('userinfo');
		$problemId=$this->getValue($_POST,$_GET,'problemId');
		$problemId=$this->getValue($_POST,$_GET,'problemId');
		$problemData=M('problem')->where(array('id'=>$problemId))->find();
		$anthor=$this->getValue($_POST,$_GET,'anthor');
		$language=$this->getValue($_POST,$_GET,'language');
		$judgeResults=$this->getValue($_POST,$_GET,'status');
		$where['user_id']=$userinfo['id'];
		$where['problem_id']  = $problemId;
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
		$where['_logic'] = 'and';
		$User = M('test_user_problem'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count = $User->where($where)->count();// 查询满足要求的总记录数
		$Page  = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		if(!$language) $language="All";
		if(!isset($judgeResults)) $judgeResults=11;
		$this->assign('problemData',$problemData);
		$this->assign("lan",$language);
		$this->assign("sta",$judgeResults);
		$this->assign("pid",$problemId);
		$this->assign("ant",$anthor);
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->myId = $userinfo['id'];
		$this->myRoot=$userinfo['root'];
		$this->display(); // 输出模板
	}
	public function showSubmit(){
		if(!session('loginStatus')){
			$this->redirect('User/showLogin');
		}
		$problemId=$_GET['id'];
		$arr=M('test_problem')->find($problemId);
		$this->assign('problemData',$arr);
		$this->display();
	}
	protected function getAllStatusStatic($problemId){
		$allStatus=$this->getStatusArray();
		$userProblem=M('test_user_problem');
		foreach($allStatus as $k => $v){
			$where=array();
			if($k!=11) $where['judge_status']=$k;
			$where['problem_id']=$problemId;
			$allStatus[$k]['sum']=$userProblem->where($where)->count();
		}
		return $allStatus;
	}
	public function showProblemStatic(){
		$statusArray=$this->getStatusArray();
		$languageArray=$this->getLanguage();
		$this->assign("statusArray",$statusArray);
		$this->assign("languageArray",$languageArray);
		$userinfo = session('userinfo');
		$problemId=$this->getValue($_POST,$_GET,'problemId');
		$problemData=M('test_problem')->where(array('id'=>$problemId))->find();
		$anthor=$this->getValue($_POST,$_GET,'anthor');
		$language=$this->getValue($_POST,$_GET,'language');
		$judgeResults=$this->getValue($_POST,$_GET,'status');
		$where['problem_id']  = $problemId;
		$parmCnt=1;
		if($language&&$language!="All") {
			$where['language']=$language;
			$parmCnt=$parmCnt+1;
		}
		if($anthor) {
			$where['nickname']  = array('like','%'.$anthor.'%');
			$parmCnt=$parmCnt+1;
		}
		if(isset($judgeResults)) {
			if($judgeResults!=11){
				$where['judge_status']=$judgeResults;
				$parmCnt=$parmCnt+1;
			}
		}
		$where['_logic'] = 'and';
		$User = M('user_problem'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count = $User->where($where)->count();// 查询满足要求的总记录数
		$Page  = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		if(!$language) $language="All";
		if(!isset($judgeResults)) $judgeResults=11;
		$statusStaticData=$this->getAllStatusStatic($problemId);
		foreach($statusStaticData as $k => $v){
			$statusStaticData[$k]['judge_status']=$k;
		}
		$this->assign('problemData',$problemData);
		$this->assign("statusStaticData",$statusStaticData);
		$this->assign("lan",$language);
		$this->assign("sta",$judgeResults);
		$this->assign("pid",$problemId);
		$this->assign("ant",$anthor);
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->myId = $userinfo['id'];
		$this->myRoot=$userinfo['root'];
		$this->display(); // 输出模板
	}
	public function changeProblem(){
		$problemId=$_GET['problemId'];
		$userinfo=session('userinfo');
		$labelId=M('problem_label')->where(array('problem_id'=>$problemId))->find()['label_id'];
		$problemIds = M('user_problem')
			->where(array('judge_status'=>0,'user_id'=>$userinfo['id']))
			->order('problem_id')
			->distinct('problem_id')
			->getField('problem_id',true);
		$where=array();
		$where['label_id']=$labelId;
		//dump($problemId);
		//array_push($problemIds,$problemId);
		if($problemIds) $where['problem_id']=array('not in',$problemIds);
		//dump($problemIds);
		$sameLabelNotSolveProblemList=M('problem_label')
			->where($where)
			->order('problem_id')
			->select();
		$problemCnt=count($sameLabelNotSolveProblemList);
		//dump($sameLabelNotSolveProblemList);
		//die;
		if($problemCnt>1){
			$randInt=rand(0,$problemCnt-1);
			$this->redirect('Problem/showProblem',array('id'=>$sameLabelNotSolveProblemList[$randInt]['problem_id']));
			//dump($randInt);
		}else {
			$newWhere['problem_id']=array('not in',$problemIds);
			$notSolveProblemList=M('problem_label')
			->where($newWhere)
			->order('problem_id')
			->select();
			$problemCnt=count($notSolveProblemList);
			$randInt=rand(0,$problemCnt-1);
			$this->redirect('Problem/showProblem',array('id'=>$notSolveProblemList[$randInt]['problem_id']));
		}
	}
	public function isAbnormal(){
		$userinfo=session('userinfo');
		
		$con['submit_time']=array('gt',time()-60);
		$con['user_id']=$userinfo['id'];
		$cnt=M('user_problem')->where($con)->count();
		
		$data['ip']=get_client_ip();
		if($cnt>=5){
			M('black_user')->add($data);
			$this->error('非法操作!');
		}
	}
	
	
	//评测相关代码

	public function showRealTimeEvaluation(){
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
			$where['problem_id']  = $problemId;
			$parmCnt=$parmCnt+1;
		}
		if($anthor) {
			$where['nickname']  = array('like','%'.$anthor.'%');
			$parmCnt=$parmCnt+1;
		}
		if($parmCnt>1) $where['_logic'] = 'and';
		
		$User = M('test_user_problem'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count = $User->where($where)->count();// 查询满足要求的总记录数
		$Page  = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $User->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//dump($count);
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
		$datapath='Data/Test/problems/'.$id;
		return $this->getfiles($datapath);
	}
	public function onlineJudge(){
		$userinfo=session('userinfo');
		if(!$userinfo){
			$this->error("别调皮啦，提交代码前，请先登录!",U('User/showLogin'));
		}
		$problemData=M('test_problem')->where('id='.$_POST['problemID'])->find();
		if(!$problemData){
			$this->error('非法操作!!!!!!!');
		}
		$caseNumber=$this->getCaseNumber($_POST['problemID']);
		if($caseNumber==0){
			$this->error("此题暂无测试数据，禁止提交!",U('Test/showProblemList'));
		}
		$number=intval($problemData['time_limit'],10);
		$timeLimit=$number;
		$memoryLimit=32768;
		$userinfo = session('userinfo');
		$code='Data/Test/code';
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
		$submitSum=M('test_user_problem')->where($condition)->Count();
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
		
		
			
		$message=M('test_user_problem');
		$userProblemId=$message->add($resultData);
		
		//$caseNumber
		$judgeDetail['user_problem_id']=$userProblemId;
		$judgeDetail['judge_status']=8;
		$judgeDetail['exe_time']=0;
		$judgeDetail['exe_memory']=0;
		$judgeDetail['score']=0;
		$judgeDetail['group_score']=intval(100/$caseNumber);
		for($i=1;$i<=$caseNumber;$i++){
			$caseInputPath='Data/Test/problems'.'/'.$_POST['problemID'].'/'.$i.'.in';
			$caseOutputPath='Data/Test/problems'.'/'.$_POST['problemID'].'/'.$i.'.out';
			$judgeDetail['group_id']=$i;
			$judgeDetail['input_file_path']=$caseInputPath;
			$judgeDetail['output_file_path']=$caseOutputPath;
			if($i==$caseNumber) $judgeDetail['group_score']=100-($caseNumber-1)*intval(100/$caseNumber);
			M('test_judge_detail')->add($judgeDetail);
		}
		$this->redirect('Test/showRealTimeEvaluation');
	}
	public function showUserCode(){
		$id=$_GET['id'];
		$condition['id']=$id;
		$userinfo=session('userinfo');
		if($userinfo['root']==0&&$userinfo['id']!=$_GET['userId']){
			$this->error('不许偷看别人的源代码哦！',U("Test/showRealTimeEvaluation"));
		}
		$data[0]=M('test_user_problem')->where($condition)->find();
		//dump($data);
		$filename = $data[0]['filepath'];

		$contents=file_get_contents($filename);
		$contents = htmlspecialchars($contents);
		$this->assign('code',$contents);
		$this->assign('judgeData',$data);
		$this->display();
	}
	public function showJudgeDetail(){
		$userProblemData=M('test_user_problem')
				->where(array('id'=>$_GET['id']))
				->find();
		$judgeData=M('test_judge_detail')
				->where(array('user_problem_id'=>$userProblemData['id']))
				->select();
		//dump($judgeData);
		$problemData=M('test_problem')
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
	public function showTestData(){
		//dump($_GET);
		$data=M('test_judge_detail')->where(array('id'=>$_GET['id']))->find();
		if($_GET['op']) $filePath=$data['output_file_path'];
		else $filePath=$data['input_file_path'];
		$contents=file_get_contents($filePath);
		$contents = htmlspecialchars($contents);
		$this->assign('text',$contents);
		$this->display();
	}
}