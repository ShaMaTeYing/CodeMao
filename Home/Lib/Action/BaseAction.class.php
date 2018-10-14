<?php
// 本类由系统自动生成，仅供测试用途
class BaseAction extends Action {
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
		$url="http://slb.cdwtrj.com:13007/api-ymxc/token/check"; 
		$param=array("token"=>$token);
		$data = json_encode($param);
		list($return_code, $return_content) = $this->http_post_data($url, $data);
		return $return_content;
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
		$url = "http://slb.cdwtrj.com:13007/api-ymxc/token/check";
		$post_data['token'] = $token;
        $o = "";
        foreach ( $post_data as $k => $v ) 
        { 
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

//		$post_data = json_encode(array('token'=>$token)); 
//		dump($post_data);
        $res = $this->requestPost($url, $post_data);       
        return $res;
    }
    public function getTestPostData(){
		$resData['errorcode']=0;
		$resData['data']['userId']='101';
		$beginLastweek=mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
		$endLastweek=mktime(23,59,59,date('m'),date('d')-date('w')+7-7,date('Y'));
		return $resData;
//		dump($resData);
	}
	public function oneMD5($value){
			$value=md5($value);
		return $value;
	}
   function _initialize(){
// 		$username = cookie('username');
//		$password = cookie('password');
//		if($username){
//			testLogin($username,$password);
//		}
		$userinfo=session('userinfo');
		$userData=M('user')->where(array('id'=>$userinfo['id']))->find();
		if($userinfo&&$userData['status']!=1)
		{
			$this->error("你已经被禁用,请联系管理员解禁！",U('User/showDisablePage'));	
		}
		
		
	   //控制切换登录窗口
		
	
		$userId=$_GET['userId'];
		$token=$_GET['token'];
//		$token='23ljwoejuoiasjfasd';
		if($token)
		{
//				$resData=$this->getPostData($token);
//				$resData = json_decode($resData,true);
//				$resData=$this->getTestPostData();
			$resData=$this->post_json_data($token);
			$resData = json_decode($resData,true);
//			dump($resData);
//			die;
			if(intval($resData['errorcode'])==0&&$resData['data']['userId'])
			{
				$userId=$resData['data']['userId'];
				$userData=M('user')->where(array('jx_id'=>$userId))->find();
				if(!$userData){
					$this->redirect('User/showLogin');
				}
				$user=M('user')->where(array('jx_id'=>$userId))->find();
				session('loginStatus',1);//显示登录成功的界面
				session('userinfo',$user);//设置userinfo的值，以便传值给模板
				
				cookie('username',$user['realname']);
//				$loginData=M('login_msg')->where(array('user_id'=>$user['id'],'status'=>'登录成功'))->select();
//				$login=$loginData[count($loginData)-1]['password'];
				cookie('password',$this->oneMD5('jx123456'));
//				dump("执行了");
//				die;
				$loginStatus=session('loginStatus');
				$this->assign('loginStatus',session('loginStatus')?session('loginStatus'):0);
				$this->redirect('Index/index');
			}
			else 
			{
//				$loginStatus=session('loginStatus');
//				$this->assign('loginStatus',session('loginStatus')?session('loginStatus'):0);
				$this->redirect('User/showLogin');
			}
		}
		else 
		{
			$loginStatus=session('loginStatus');
			$this->assign('loginStatus',session('loginStatus')?session('loginStatus'):0);
			if(session('loginStatus'))//登录成功则传值给模板变量
			{
				$this->assign('userinfoData',session('userinfo'));
			}
			else $this->redirect('User/showLogin');
		}
		
   }
}