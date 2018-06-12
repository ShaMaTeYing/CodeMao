<?php
	function encode(){
		return 1;
	}
	function viewAdd($a,$b){
		echo(intval($a)+intval($b)); 
	}
	function getRatio($a,$b){
		if($b==0) echo(0);
		else {
		
			echo(sprintf("%.2f", doubleval($a)/doubleval($b)*100));
		}
	}
	function myMD5($value){
		for($i=1;$i<=5;$i++){
			$value=md5($value);
		}
		return $value;
	}
	function saveViolationMessage($information){
		$userinfo=session('userinfo');
		$violation_msg_data = array();//记录登录信息
		$violation_msg_data['ip']=get_client_ip();//获取ip
		import('ORG.Net.IpLocation');// 导入IpLocation类
		$Ip = new IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
		$location= $Ip->getlocation(get_client_ip()); // 获取某个IP地址所在的位置
		$violation_msg_data['area'] =  $location['country'].$location['area'];
		$violation_msg_data['operate_time']=time();
		$violation_msg_data['user_id']=$userinfo['id'];
		$violation_msg_data['information']=$information;
		//dump($violation_msg_data);
		//die;
		M('violation')->add($violation_msg_data);
	}
?>
