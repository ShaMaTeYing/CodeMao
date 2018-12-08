<?php
// 本类由系统自动生成，仅供测试用途
class ProblemAction extends BaseAction {
	
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
    protected function queryProblemTips($tipsData,$problemId){
    	foreach($tipsData as $k => $v){
    		if($tipsData[$k]['problem_id']==$problemId) return $tipsData[$k];
    	}
    	return null;
    }
    public function showProblemList(){
    	$query=array();
		$userinfo=session('userinfo');
		$tipsData=M('tips')->where(array('user_id'=>$userinfo['id']))->select();
//		dump($tipsData);
		$query['user_id']=$userinfo['id'];
		$userProblem=M('ladder_user_problem')->where($query)->field('problem_id')->select();
		foreach($userProblem as $k => $v){
			$submitProblem[$k]=$userProblem[$k]['problem_id'];
		}
		$query=array();
        if($submitProblem)
		    $query['problem_id']=array('not in',$submitProblem);
		$notSubmitProblem=M('ladder_contest_problem')->where($query)->field('problem_id')->select();
		//dump($notSubmitProblem);
		$problemMark=array();
		foreach($notSubmitProblem as $k => $v){
			$problemMark[$notSubmitProblem[$k]['problem_id']]=1;
		}
		//dump($problemMark);
		$userinfo = session('userinfo');
		$value=$_POST['value'];
		//dump($_GET);
		$labelId=$_GET['label_id'];
		//dump($labelId);
		if($labelId){
			$list=M("table")->table('problem a,problem_label b')
			->where("a.id=b.problem_id and b.label_id=".$labelId." and b.status=0 and a.status=1")
			->select();
		
			//dump($list);
			
			//$User = M('problem'); // 实例化User对象
			import('ORG.Util.Page');// 导入分页类
			$count = M("table")->table('problem a,problem_label b')
			->where("a.id=b.problem_id and b.label_id=".$labelId." and b.status=0 and a.status=1")->count();// 查询满足要求的总记录数
			$Page  = new Page($count,50);// 实例化分页类 传入总记录数和每页显示的记录数
			
			$show  = $Page->show();// 分页显示输出
			//dump($list);
			// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
			$list = M("table")->table('problem a,problem_label b')
			->where("a.id=b.problem_id and b.label_id=".$labelId." and b.status=0 and a.status=1")->limit($Page->firstRow.','.$Page->listRows)
			->order('problem_id')
			->select();
			//dump($list);
			$listIds = M("table")->table('problem a,problem_label b')
			->where("a.id=b.problem_id and b.label_id=".$labelId." and b.status=0 and a.status=1")
			->limit($Page->firstRow.','.$Page->listRows)
			->getField('problem_id',true);
//			foreach($list as $key => $value){
//				$list['id']=$list['problem_id'];
//			}
			//dump($listIds);
		}
		else {
			$where['title']  = array('like','%'.$value.'%');
			$where['id']  = array('like','%'.$value.'%');
			$where['description']  = array('like','%'.$value.'%');
			$where['_logic'] = 'or';
			$map['_complex'] = $where;
			$map['status']=1;
			//$problemData=M('problem')->select();
			//$this->assign('problemData',$problemData);
			
			$User = M('problem'); // 实例化User对象
			import('ORG.Util.Page');// 导入分页类
			$count = $User->where($map)->count();// 查询满足要求的总记录数
			$Page  = new Page($count,50);// 实例化分页类 传入总记录数和每页显示的记录数
			$show  = $Page->show();// 分页显示输出
			// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
			$list = $User->where($map)->order('problem_mark asc')->limit($Page->firstRow.','.$Page->listRows)->select();
		//	dump($Page);
			$listIds = $User
			->where($map)
			->limit($Page->firstRow.','.$Page->listRows)
			->getField('id',true);
			//dump($listIds);
		}		
		//dump($listIds);
		$userDo = M('user_problem')
			->where(array('problem_id'=>array(IN,$listIds),'user_id'=>$userinfo['id']))
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
		
		foreach($list as $k1 => $v1){
			$queryTips=$this->queryProblemTips($tipsData,$list[$k1]['id']);
			if($queryTips){
				$list[$k1]['tip']=$queryTips['tip'];
				if($queryTips['score']) $list[$k1]['get_score']=$queryTips['score'];
				else $list[$k1]['get_score']=0;
			}else{
				$list[$k1]['tip']=0;
				$list[$k1]['get_score']=0;
			}
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
		
//		$list=$this->my_sort($list,'problem_mark',SORT_DESC,SORT_STRING);
//		dump($list);
		$this->assign('problemData',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出

		
		//提取标签数据
		$Label=M('label_info');
		$LabelAllData=$Label->where("status=0")->select();
		$labelData=array(array());
		foreach($LabelAllData as $k => $v){
			if($LabelAllData[$k]['label_name']=="") continue;
			$labelData[$k]['label_name']=$v['label_name'];
			$labelData[$k]['label_id']=$v['id'];
			$tmp=M('problem_label')->where(array('label_id'=>$v['id'],'status'=>0))->count();
			if($v['status']){
				$labelData[$k]['problem_number']=0;
			}
			else {
				$labelData[$k]['problem_number']=$tmp;
			}
		}
		$labelData=$this->my_sort($labelData,'problem_number',SORT_DESC);
		$this->assign('labelData',$labelData);
		
		//dump($labelData);

		
		$this->display();
		
		//$this->display();
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
		$arr=M('problem')->find($problemId);
		$tipsData=json_decode($arr['tips']);
		//dump($tipsData);
		$arr['label']=$tipsData->label;
		$arr['idea']=$tipsData->idea;
		$arr['stdexe']=htmlspecialchars($tipsData->stdexe);
		//dump($arr);
		//die;
		$ans=M('ladder_contest_problem')->where(array('problem_id'=>$problemId))->find();
		if($ans){
			$record=M('ladder_user_problem')->where(array('user_id'=>$userinfo['id'],'problem_id'=>$problemId))->find();
			if(!$record){
				saveViolationMessage('尝试输入天梯赛题目 '.$problemId);
				$this->error('不要调皮，乱输入题号！',U("Problem/showProblemList"));
			}
		}
		if(!$arr){
			$this->error('不要调皮，乱输入题号！',U("Problem/showProblemList"));
		}
		$lastLan=session('lastLan');
		if(!$lastLan) $lastLan="C++";
		$lanData=array("C++","C","PASCAL");
		
		$tipsData=M('tips')->where(array('user_id'=>$userinfo['id'],'problem_id'=>$problemId))->find();
		if($tipsData['tip']==1) $tipsData['radio']=80;
		else if($tipsData['tip']==2) $tipsData['radio']=50;
		else if($tipsData['tip']==3) $tipsData['radio']=0;
		else if($tipsData['tip']==4) $tipsData['radio']=0;
		$this->assign('tipsData',$tipsData);
		$this->assign('lanData',$lanData);
		$this->assign('lastLan',$lastLan);
		$this->assign('problemData',$arr);
		$this->assign('userinfo',$userinfo);
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
		$User = M('user_problem'); // 实例化User对象
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
		$arr=M('problem')->find($problemId);
		$this->assign('problemData',$arr);
		$this->display();
	}
	protected function getAllStatusStatic($problemId){
		$allStatus=$this->getStatusArray();
		$userProblem=M('user_problem');
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
		$problemData=M('problem')->where(array('id'=>$problemId))->find();
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
	public function changeProblemTips(){
		$pid=$_POST['pid'];
		$tip=$_POST['tip'];
		$tips=M('tips');
		$userinfo=session('userinfo');
		$saveData['user_id']=$userinfo['id'];
		$saveData['problem_id']=$pid;
		$saveData['tip']=$tip;
		$tipsData=$tips->where(array('problem_id'=>$pid,'user_id'=>$userinfo['id']))->find();
		if(!$tipsData){//如果不存在
			$tips->add($saveData);
		}else {
			if($tipsData['tip']<$tip){
				$tipsData['tip']=$tip;
				$tips->save($tipsData);
			}
		}
	}
	public function showStdexe(){
		$userinfo=session('userinfo');
		$tipsData=M('tips')->where(array('user_id'=>$userinfo['id'],'problem_id'=>$_GET['id']))->find();
		if(!$tipsData||$tipsData['tip']!=3) {
			$this->redirect('Problem/showProblem',array('id'=>$_GET['id']));
		}
		$arr=M('problem')->find($_GET['id']);
		$tipsData=json_decode($arr['tips']);
		//dump($tipsData);
		$arr['label']=$tipsData->label;
		$arr['idea']=$tipsData->idea;
		$arr['stdexe']=$tipsData->stdexe;
//		dump($arr);
		$this->assign('tips',$arr);// 赋值分页输出
		$this->display(); // 输出模板
	}
	
	public function showVideo(){
		$problemData=M('problem')->where(array('id'=>$_GET['id']))->find();
		$this->assign('problemData',$problemData);// 赋值分页输出
		$this->display(); // 输出模板
	}
	public function testAjax(){
		$data=$_POST;
		$this->ajaxReturn($data,'JSON');
	}
}