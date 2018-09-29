<?php
// 本类由系统自动生成，仅供测试用途
class APIAction extends Action {
	public function getProblemData(){
		$where['title']  = $_POST['title'];
		$where['id']  = $_POST['problem_id'];
		$where['problem_mark']  = $_POST['problem_mark'];
		$where['_logic'] = 'or';
		$problemData=M('problem')->where($where)->find();
		if($problemData) {
			if($problemData['status']==0){
				$problemData=null;
				$problemData['status']=2;
				$problemData['info']="The problem has been disabled";
			}else {
				if(strpos($problemData['sample_input'],"无"))
				{
					$problemData['sample_input']="";
				}
				$problemData['status']=1;
				$problemData['info']="The data of the topic was successfully obtained";
			}
		}
		else {
			$problemData['status']=0;
			$problemData['info']="The problem_id or problem_mark or title does not exist";
		}
		$this->ajaxReturn($problemData,'JSON');
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
	public function onlineJudge($userinfo,$problemData,$code,$language,$ext,$caseNumber){
		$timeLimit=intval($problemData['time_limit'],10);
		$memoryLimit=intval($problemData['memory_limit'],10);
		$codePath='Data/Library/code';
		if(!file_exists($codePath)) {
			mkdir($codePath);
			chmod($codePath, 0777);
		}
		if(!file_exists($codePath.'/'.$userinfo['username'])) {
			mkdir($codePath.'/'.$userinfo['username']);
			chmod($codePath.'/'.$userinfo['username'], 0777);
		}
		$condition['user_id']=$userinfo['id'];
		$condition['problem_id']=$problemData['id'];
		$submitSum=M('user_problem')->where($condition)->Count();
		$submitSum=$submitSum+1;
		$filename = $codePath.'/'.$userinfo['username'].'/'.$_POST['problemID'].'_'.$submitSum.$ext;
		file_put_contents($filename, $code);
		chmod($filename, 0777);
		$resultData['user_id']=$userinfo['id'];
		$resultData['problem_id']=$problemData['id'];
		$resultData['submit_time']=time();
		$resultData['judge_status']=8;
		$resultData['exe_time']=0;
		$resultData['exe_memory']=0;
		$resultData['code_len']=strlen($code);
		$resultData['language']=$language;
		$resultData['nickname']=$userinfo['nickname'];
		$resultData['filepath']=$filename;
		$userProblemId=M('user_problem')->add($resultData);
		$judgeDetail['user_problem_id']=$userProblemId;
		$judgeDetail['judge_status']=8;
		$judgeDetail['exe_time']=0;
		$judgeDetail['exe_memory']=0;
		$judgeDetail['score']=0;
		$judgeDetail['group_score']=10;
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
//		dump($tip);
//		die;
		if(!$tip){
			$tips['user_id']=$userinfo['id'];
			$tips['problem_id']=$_POST['problemID'];
			$tips['tip']=0;
			M('tips')->add($tips);
		}
		return $userProblemId;
	}
	public function submitUserCode(){
		$data = array();
		$userinfo = M('user')->where(array('realname'=>$_POST['realname']))->find();
		if(!$userinfo){
			$data['status'] = 0;
			$data['info'] = 'User does not exist';
			$this->ajaxReturn($data,'JSON');
		}
		$problemData = M('problem')->where(array('id'=>$_POST['problem_id']))->find();
		if(!$problemData || $problemData['status']==0){
			$data['status'] = 1;
			$data['info'] = 'The problem does not exist or has been disabled';
			$this->ajaxReturn($data,'JSON');
		}
		$code = $_POST['code'];
		if(strstr($code,"freopen")||strstr($code,"fopen")){
			$data['status'] = 2;
			$data['info'] = 'The code contains an offensive function that does not allow submission';
			$this->ajaxReturn($data,'JSON');
		}
		$caseNumber=$this->getCaseNumber($problemData['id']);
		if($caseNumber==0){
			$data['status'] = 3;
			$data['info'] = 'There is no data for this problem, no submission is allowed';
			$this->ajaxReturn($data,'JSON');
		}
		$language=$_POST['language'];
		if($language=='C++'){
			$ext='.cpp';
		}else if($language=='C'){
			$ext='.c';
		}else if($language=='PASCAL') {
			$ext='.pas';
		}else {
			$data['status'] = 4;
			$data['info'] = 'Programming language not provided';
			$this->ajaxReturn($data,'JSON');
		}
		$userProblemId=$this->onlineJudge($userinfo,$problemData,$code,$language,$ext,$caseNumber);
		$data['status'] = 5;
		$data['info'] = 'Submitted successfully';
		$data['judge_record_id'] = $userProblemId;
		$this->ajaxReturn($data,'JSON');
	}
	protected function getStatus($judgeStatus){
		if($judgeStatus==0) return "Accepted";
		else if($judgeStatus==1) return "Wrong Answer";
		else if($judgeStatus==2) return "Time Limit Exceeded";
		else if($judgeStatus==3) return "Memory Limit Exceeded";
		else if($judgeStatus==4) return "Runtime Error";
		else if($judgeStatus==5) return "Compilation Error";
		else if($judgeStatus==6) return "Output Limit Exceeded";
		else if($judgeStatus==7) return "Input Limit Exceeded";
		else if($judgeStatus==8) return "Pending";
		else if($judgeStatus==9) return "Compiling";
		else if($judgeStatus==10) return "Runing";
		else if($judgeStatus==11) return "Presentation Error";
	}
	public function getJudgeMessage(){
		$judgeRecord = M('user_problem')->where(array('id'=>$_POST['judge_record_id']))->find();
		if(!$judgeRecord){
			$judgeRecord['status'] = 0;
			$judgeRecord['info'] = 'judge record does not exist';
			$this->ajaxReturn($judgeRecord,'JSON');
		}
		$judgeStatus=intval($judgeRecord['judge_status'],10);
		$judgeRecord['status'] = 1;
		$judgeRecord['judge_status']=$this->getStatus($judgeStatus);
		$judgeRecord['info'] = 'Successfully obtained the evaluation record';
		$this->ajaxReturn($judgeRecord,'JSON');
	}
	public function requestPost($url = '', $param = '') {
//		dump("进入");
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return $data;
    }
    public function getPostData($token){
//    	$url  = 'http://codemao.com/index.php/API/getJudgeMessage';
//		$post_data['judge_record_id']       = '5649';
		$url = "http://dev.cdwtrj.com:13007/api-ymxc/token/check";
		$post_data['token'] = $token;
        $o = "";
        foreach ( $post_data as $k => $v ) 
        { 
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $res = $this->requestPost($url, $post_data);       
        return $res;
    }
    public function oneMD5($value){
			$value=md5($value);
		return $value;
	}
	public function jxLogin(){
		$token = $_POST['token'];
		$resData=$this->getPostData($token);
		$resData = json_decode($resData,true);
		if(intval($resData['errorcode'])==0){
			$userId=$resData['data']['userId'];
			$userData=M('user')->where(array('jx_id'=>$userId))->find();
			if(!$userData){
				$userData['username'] = "jx".$userId;
				$userData['password'] = "jx123456";
				$userData['status'] = "1";
				$userData['school'] = "嘉祥集团";
				$userData['motto'] = "jx".$userId;
				$userData['mail'] = "jx@onecode.com.cn";
				$userData['realname'] = "jx".$userId;
				$userData['major'] = "jx".$userId;
				$userData['nickname'] = "jx".$userId;
				$userData['register_time'] = time();
				$userData['jx_id'] = $userId;
				M('user')->add($userData);
			}
			$user=M('user')->where(array('jx_id'=>$userId))->find();
			session('loginStatus',1);//显示登录成功的界面
			session('userinfo',$user);//设置userinfo的值，以便传值给模板
			cookie('username',$user['realname']);
			cookie('password',$this->oneMD5($_POST['password']));
			//$this->redirect('Index/index');
			$data['status'] = 0;
			$data['info'] = 'Login successfully';
			$data['url'] = 'http://course.onecode.com.cn/CodeMao/index.php/Index/index';
			$this->ajaxReturn($data,'JSON');
		}else {
			$data['status'] = 1;
			$data['info'] = 'Login failed';
			$this->ajaxReturn($data,'JSON');
		}
	}
	/*post  token
		return 
		{
			rank:13,
			all_submit:11,
			all_ac:9,
			every_level_submit:{"0":1,"1":2},
			every_level_ac:{"0":1,"1":2},
			evaluate:"非常好"
		}
		 */
	public function jxWeekStatistics(){
		$data['status'] = 1;
		$data['info'] = 'Query successfully';
		$data['rank'] = 1;
		$data['all_submit'] = '10';
		$data['all_ac'] = 6;
		$data['every_level_submit'] = '{"0":1,"1":2}';
		$data['every_level_ac'] = '{"0":1,"1":2}';
		$data['evaluate'] = "非常好";
		$this->ajaxReturn($data,'JSON');
	}
}