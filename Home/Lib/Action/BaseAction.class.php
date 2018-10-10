<?php
// 本类由系统自动生成，仅供测试用途
class BaseAction extends Action {
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
		$loginStatus=session('loginStatus');
		
	   //控制切换登录窗口
		$this->assign('loginStatus',session('loginStatus')?session('loginStatus'):0);
		if(session('loginStatus'))//登录成功则传值给模板变量
		{
			$this->assign('userinfoData',session('userinfo'));
		}
		else 
		{
			$userId=$_GET['userId'];
			$token=$_GET['token'];
			if($token)
			{
				$resData=$this->getPostData($token);
				$resData = json_decode($resData,true);
//				$resData=$this->getTestPostData();
				if(intval($resData['errorcode'])==0)
				{
					$userId=$resData['data']['userId'];
					$user=M('user')->where(array('jx_id'=>$userId))->find();
					session('loginStatus',1);//显示登录成功的界面
					session('userinfo',$user);//设置userinfo的值，以便传值给模板
					cookie('username',$user['realname']);
					cookie('password',$this->oneMD5($_POST['password']));
				}
				else 
				{
					$this->redirect('User/showLogin');
				}
			}
			else 
			{
				$this->redirect('User/showLogin');
			}
		}
   }
}