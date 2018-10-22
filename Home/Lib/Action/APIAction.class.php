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
	public function getTestPostData(){
		$resData['errorcode']=0;
		$resData['data']['userId']='101';
		$beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
		$endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
		return $resData;
//		dump($resData);
	}
//	public function jxLogin(){
//		$token = $_POST['token'];
//		if(!$token) $token = $_GET['token'];
//		$resData=$this->getPostData($token);
//		$resData = json_decode($resData,true);
////		$resData=$this->getTestPostData();
//		if(intval($resData['errorcode'])==0){
//			$userId=$resData['data']['userId'];
//			$userData=M('user')->where(array('jx_id'=>$userId))->find();
//			if(!$userData){
//				$userData['username'] = "jx".$userId;
//				$userData['password'] = "jx123456";
//				$userData['status'] = "1";
//				$userData['school'] = "嘉祥集团";
//				$userData['motto'] = "jx".$userId;
//				$userData['mail'] = "jx@onecode.com.cn";
//				$userData['realname'] = "jx".$userId;
//				$userData['major'] = "jx".$userId;
//				$userData['nickname'] = "jx".$userId;
//				$userData['register_time'] = time();
//				$userData['jx_id'] = $userId;
//				M('user')->add($userData);
//			}
//			$user=M('user')->where(array('jx_id'=>$userId))->find();
//			session('loginStatus',1);//显示登录成功的界面
//			session('userinfo',$user);//设置userinfo的值，以便传值给模板
//			cookie('username',$user['realname']);
//			cookie('password',$this->oneMD5($_POST['password']));
//			//$this->redirect('Index/index');
//			$data['status'] = 0;
//			$data['info'] = 'Login successfully';
//			$data['url'] = 'http://course.onecode.com.cn/index.php/Index/index';
//			$this->ajaxReturn($data,'JSON');
//		}else {
//			$data['status'] = 1;
//			$data['info'] = 'Login failed';
//			$this->ajaxReturn($data,'JSON');
//		}
//	}
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
	public function getEveryLevelSubmit($userData,$beginLastweek,$endLastweek){
		// $beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
		// $endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
		$where['user_id']=$userData['id'];
		$where['submit_time']=array(array('egt',$beginLastweek),array('elt',$endLastweek));
		$submitData=M('user_problem')->where($where)->select();
		$problem=M('problem');
	
		foreach($submitData as $k => $v){
			$problemData = $problem->where(array('id'=>$submitData[$k]['problem_id']))->find();
			$everyLevelSubmit['level_'.$problemData['difficulty']]+=1;
		}
		return $everyLevelSubmit;
	}
	public function getEveryLevelAc($userData,$beginLastweek,$endLastweek){
		// $beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
		// $endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
		$where['user_id']=$userData['id'];
		$where['submit_time']=array(array('egt',$beginLastweek),array('elt',$endLastweek));
		$where['judge_status']=0;
		$submitData=M('user_problem')->where($where)->select();
		$problem=M('problem');
		foreach($submitData as $k => $v){
			$problemData = $problem->where(array('id'=>$submitData[$k]['problem_id']))->find();
			$everyLevelAc['level_'.$problemData['difficulty']]+=1;
		}
		return $everyLevelAc;
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
    /*
     	排序思路，tips再开一个字段score记录用户对应题目的得分，判题时，第一次AC，根据tip信息计算出分数写入该字段
     	判题端暂时未写
     * */
	/*显示所有用户*/
//	public function showAllUserRank($jx_id){
//		$map['status']  = 1;
//		$User = M('User'); // 实例化User对象
//		$list = $User->where($map)->select();
//		$tips = M('tips');
//      for($i=0;$i<count($list);$i++){
//          $list[$i]['score']=$tips->where(array('user_id'=>$list[$i]['id']))->sum('score');
//      }
////      dump($list);
//      $list=$this->sortArrByManyField($list,'score',SORT_DESC,'solve_problem',SORT_DESC,'submissions',SORT_ASC);
////		dump($list);
//		for($i=0;$i<count($list);$i++){
//			if($i>0&&$list[$i]['solve_problem']==$list[$i-1]['solve_problem']&&$list[$i]['submissions']==$list[$i-1]['submissions']){
//				$list[$i]['rank']=$list[$i-1]['rank'];
//			}else {
//				$list[$i]['rank']=$Page->firstRow+$i+1;
//			}
//			if($jx_id==$list[$i]['jx_id']) return $list[$i]['rank'];
//		}
//		//$this->assign('color','red');
//		
//	}
	public function showAllUserRank($jx_id){
	
		$sortParam = $_POST['sort_param'];
		
		$where['nickname']  = array('like','%'.$sortParam.'%');
		$where['solve_problem']  = array('like','%'.$sortParam.'%');
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
		$map['status']  = 1;
		
		$User = M('User'); // 实例化User对象
		$list = $User->where($map)->select();
		for($i=0;$i<count($list);$i++){
			$list[$i]['score']=$list[$i]['library_score']+$list[$i]['ladder_score'];
		}
        $list=$this->sortArrByManyField($list,'score',SORT_DESC,'solve_problem',SORT_DESC,'submissions',SORT_ASC);
		for($i=0;$i<count($list);$i++){
			if($i>0&&$list[$i]['solve_problem']==$list[$i-1]['solve_problem']&&$list[$i]['submissions']==$list[$i-1]['submissions']){
				$list[$i]['rank']=$list[$i-1]['rank'];
			}else {
				$list[$i]['rank']=$Page->firstRow+$i+1;
			}
			if($list[$i]['rank']<=5){
				$list[$i]['color']='red';
			}else if($list[$i]['rank']<=10){
				$list[$i]['color']='orange';
			}else if($list[$i]['rank']<=15){
				$list[$i]['color']='purple';
			}else {
				$list[$i]['color']='blue';
			}
			$list[$i]['score']=$list[$i]['library_score']+$list[$i]['ladder_score'];
			if($jx_id==$list[$i]['jx_id']) return $list[$i]['rank'];
		}
		
		
	}
	public function test_get_rank(){
		dump($_GET['id']);
		$data=$this->showAllUserRank($_GET['id']);
		dump($data);
	}
	public function http_post_data($url, $data_string) 
	{	
		$ch = curl_init();	
		curl_setopt($ch, CURLOPT_POST, 1);	
		curl_setopt($ch, CURLOPT_URL, $url);	
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(		"Content-Type: application/json; charset=utf-8",		"Content-Length: " . strlen($data_string))	);	
		ob_start();	
		curl_exec($ch);	
		$return_content = ob_get_contents();	
		ob_end_clean();	
		$return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);	
		return array($return_code, $return_content);
	}
	public function post_json_data($token)
	{
		$url="http://dev.cdwtrj.com:13007/api-ymxc/token/check"; 
		$param=array("token"=>$token);
		$data = json_encode($param);
		list($return_code, $return_content) = $this->http_post_data($url, $data);
		return $return_content;
	}
	public function jxWeekStatistics(){
		$beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
		$endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
		$userId = $_POST['userId'];
		if(!$userId&&$_GET['userId']) $userId = $_GET['userId'];
		if(!$userId) {
			$psotdata=$GLOBALS['HTTP_RAW_POST_DATA'];
			$psotdata = json_decode($psotdata,true);   //格式化
			$userId=$psotdata['userId'];
		}
		$userData = M('user')->where(array('jx_id'=>$userId))->find();
//		dump($userId);
//		dump($userData);
//		die;
		if($userData){
//			dump('ssss');
//			die;
			$data['status'] = 0;
			$data['info'] = 'Query successfully';
			$data['rank'] = $this->showAllUserRank($userData['jx_id']);
			$where['user_id']=$userData['id'];
			$where['submit_time']=array(array('egt',$beginLastweek),array('elt',$endLastweek));
			$data['all_submit'] = M('user_problem')->where($where)->count();
			$where['judge_status']=0;
			$data['all_ac'] = M('user_problem')->where($where)->count();
			$data['every_level_submit'] = $this->getEveryLevelSubmit($userData,$beginLastweek,$endLastweek);
//			dump('ssss2');
			$data['every_level_ac'] = $this->getEveryLevelAc($userData,$beginLastweek,$endLastweek);
//			dump('ssss3');
			if($data['all_ac']<5){
				$data['evaluate'] = "题目写得有点少哦，还需要更加努力！";
			}else if($data['all_ac']<10){
				$data['evaluate'] = "这周表现不错，希望再接再厉！";
			}else if($data['all_ac']<15){
				$data['evaluate'] = "这周已经很少有人比你更加努力了，继续加油前进！";
			}else if($data['all_ac']<20){
				$data['evaluate'] = "你是个有天赋的孩子，再接再厉吧！";
			}else {
				$data['evaluate'] = "你已经无人能敌了，希望继续保持！";
			}
		}else {
			$data['status'] = 1;
			$data['info'] = 'Query failed';
		}
//		dump($data);
//		die;
// $data['userId']=$psotdata['userId'];
		$this->ajaxReturn($data,'JSON');
	}
	public function jxThisWeekStatistics(){
			$beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
			$endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y'));
			$userId = $_POST['userId'];
			if(!$userId&&$_GET['userId']) $userId = $_GET['userId'];
			if(!$userId) {
				$psotdata=$GLOBALS['HTTP_RAW_POST_DATA'];
				$psotdata = json_decode($psotdata,true);   //格式化
				$userId=$psotdata['userId'];
			}
			$userData = M('user')->where(array('jx_id'=>$userId))->find();
	//		dump($userId);
	//		dump($userData);
	//		die;
			if($userData){
	//			dump('ssss');
	//			die;
				$data['status'] = 0;
				$data['info'] = 'Query successfully';
				$data['rank'] = $this->showAllUserRank($userData['jx_id']);
				$where['user_id']=$userData['id'];
				$where['submit_time']=array(array('egt',$beginLastweek),array('elt',$endLastweek));
				$data['all_submit'] = M('user_problem')->where($where)->count();
				$where['judge_status']=0;
				$data['all_ac'] = M('user_problem')->where($where)->count();
				$data['every_level_submit'] = $this->getEveryLevelSubmit($userData,$beginLastweek,$endLastweek);
	//			dump('ssss2');
				$data['every_level_ac'] = $this->getEveryLevelAc($userData,$beginLastweek,$endLastweek);
	//			dump('ssss3');
				if($data['all_ac']<5){
					$data['evaluate'] = "题目写得有点少哦，还需要更加努力！";
				}else if($data['all_ac']<10){
					$data['evaluate'] = "这周表现不错，希望再接再厉！";
				}else if($data['all_ac']<15){
					$data['evaluate'] = "这周已经很少有人比你更加努力了，继续加油前进！";
				}else if($data['all_ac']<20){
					$data['evaluate'] = "你是个有天赋的孩子，再接再厉吧！";
				}else {
					$data['evaluate'] = "你已经无人能敌了，希望继续保持！";
				}
			}else {
				$data['status'] = 1;
				$data['info'] = 'Query failed';
			}
	//		dump($data);
	//		die;
	// $data['userId']=$psotdata['userId'];
			$this->ajaxReturn($data,'JSON');
		}

	public function myMD5($value){
		for($i=1;$i<=5;$i++){
			$value=md5($value);
		}
		return $value;
	}
	public function userData(){
		$userId=array("60680", "15064", "10416", "10010", "6384", "4243", "20042", "16247", "15535",
		 "7196", "16902", "21727", "16243", "17846", "57171", "47291", "18979", "16243", "13402", 
		 "12239", "16764", "7909", "18747", "46509", "60432", "21293", "18486", "16666", "5909", "12589", 
		 "19023", "46332", "46921", "46377", "46540", "46663", "46606", "47166", "46844", "46661", "51094",
		  "46322", "46635", "46670", "46849", "46907", "46913", "46948", "46867", "47419", "15694", "47137",
		   "47297", "46512", "46625", "14993", "11471", "16137", "46363", "46642", "51857", "47030", "5127",
		    "13709", "19216", "46446", "46507", "46305", "46421", "46859", "47297", "8324", "46690", "47146",
		     "47421", "47470", "47497", "6324", "21764", "47338", "15802", "51440", "4624", "46371", "60153",
		      "18260", "57239", "11751", "11256", "55817", "6306", "49116", "46405", "13086");
		$username=array("陈飞羽", "于晴", "雷诣桁", "黄玉璐", "吴玮豪", "邓响", "王一新", "朱梓瑄", "刘亦航", "凌圣宗", "谢宜珎",
		 "谭宇辰", "何天阳", "秦宇亨", "李砚鹏", "谷俐", "樊思彤", "何文心", "杨宛融", "万众", "管浩霖", "廖霈萱", "李彦战", "宋明哲",
		  "张一天", "何锦曈", "陆榕国", "覃云瀚", "李卓恒", "苏彦旭", "江妍洁", "文泓又", "张新昊", "唐子涵", "樊易", "石星见", "张浩麟", 
		  "张家乐", "赵建亦", "熊涵语", "张峻宁", "丰誉航", "唐熙远", "金彤", "伍宇昶", "袁炜蘅", "涂羽桐", "王子晋", "姜冰彬", "王鹏宇", 
		  "吴莛宣", "谢明昊", "运动孩", "薛淞阳", "李劲汐", "康焜翔", "钟林珂", "高宇星", "杜彦希", "曾彬洁", "李彦宏", "唐际森", "俞一博",
		   "任橹汐", "刘涛萌", "冯哲雅", "杨沛希", "黄译萱", "付佳利", "张峻涵", "提交", "张怡可", "王馨睿", "代睿麟", "李宇轩", "唐志雄",
		    "张杰栋", "易泽宇", "何家乐", "陶俊驰", "刘思睿", "何沛联", "林靖然", "邓淳予", "严浩峰", "唐直", "何宗宪", "谢沐君", 
		    "李卓衡", "陈昶言", "罗长青", "邢陈子轩", "谭钧议", "蒲浩歌" );
		for($i=0;$i<count($userId);$i++){
			$this->jxUserRegister($userId[$i],$username[$i]);
		}
		
	}
	public function jxUserRegister($userId,$username){
//		$userId = $_POST['userId'];
//		$username = $_POST['username'];
		if(M('user')->where(array('jx_id'=>$userId))->find()) return ;
		$userData['username'] = $username;
		$userData['password'] = $this->myMD5("jx123456");
		$userData['status'] = "1";
		$userData['school'] = "嘉祥集团";
		$userData['motto'] = "该用户很懒，什么都没有留下。";
		$userData['mail'] = "jx@onecode.com.cn";
		$userData['realname'] = $username;
		$userData['major'] = "无。";
		$userData['nickname'] = $username;
		$userData['register_time'] = time();
		$userData['jx_id'] = $userId;
		$Id=M('user')->add($userData);
		$userGroup['user_id']=$Id;
		$userGroup['group_id']=3;
		M('user_group')->add($userGroup);
	}
}