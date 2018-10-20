<?php
// 本类由系统自动生成，仅供测试用途
class TrainAdminAction extends BaseAction {
	/* 构造函数 */
	function _initialize(){
		$userinfo=session('userinfo');
		if(!$userinfo||$userinfo['root']<1){
			$this->error("非法访问，请先登录!",U('User/showLogin'));
		}
		$login=M('user')->where(array('id'=>$userinfo['id']))->find();
		$loginStatus=session('loginStatus');
	   //控制切换登录窗口
		$this->assign('loginStatus',session('loginStatus')?session('loginStatus'):0);
		if(session('loginStatus'))//登录成功则传值给模板变量
		{
			$this->assign('userinfoData',session('userinfo'));
		}
	}
	function showTrainAdminIndexPage(){
		$courseData=M('course')->select();
		$this->assign('courseData',$courseData);
		$this->display();
	}
	function showModifyCoursePage(){
		$courseData=M('course')->where(array('id'=>$_GET['id']))->find();
		$this->assign('courseData',$courseData);
		$this->display();
	}
	function modifyCourseData(){
		$url=$this->upload();
		$saveData=$_POST;
		if($url!=-1)
		{
			if(isset($url['course_pic'])) $saveData['course_pic']=$url['course_pic'];
			if(isset($url['teacher_avater'])) $saveData['teacher_avater']=$url['teacher_avater'];
		}
		$res=M('course')->save($saveData);
		if($res) $this->redirect('showTrainAdminIndexPage');
		else $this->error("修改失败！",U('showTrainAdminIndexPage'));
	}
	function upload()
	{
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		$upload->maxSize  = 20971520 ;// 设置附件上传大小 2M
		$upload->allowExts = explode(',','jpg,gif,png,jpeg');
		$upload->savePath = 'Public/img/';	//设置上传路径
		$upload->saveRule = 'uniqid';
		if(!$upload->upload()){
			return -1;
		}else{
			$uploadInfo = $upload->getUploadFileInfo();
			foreach($uploadInfo as $k => $v){
				$url[$uploadInfo[$k]['key']]='/CodeMao/'.$uploadInfo[$k]['savepath'].$uploadInfo[$k]['savename'];
			}

		}
		return $url;
	}
	function showCourseSectionPage(){
		if(!isset($_GET['id'])) $_GET['id']=1;
		$courseSectionData=M('course_section')->where(array('course_id'=>$_GET['id']))->order(array('order'))->select();
		$order['min']=1;$order['max']=count($courseSectionData);
		$this->assign('courseSectionData',$courseSectionData);
		$this->assign('order',$order);
//		dump($courseSectionData);
		$course = M('course')->where(array('id'=>$_GET['id']))->find();
		$this->assign('course',$course);
		$this->display();
	}
	function modifySectionData(){
		$saveData['id']=$_GET['id'];
		$saveData['name']=$_GET['name'];
		$res=M('course_section')->save($saveData);
		if($res) $this->redirect('showCourseSectionPage');
		else $this->error("修改失败！",U('showCourseSectionPage'));
	}
	function switchStatic(){
		$courseSectionData=M('course_section')->where(array('id'=>$_GET['id']))->find();
		$courseSectionData['status']=(string)(1-intval($courseSectionData['status']));
		$res=M('course_section')->save($courseSectionData);
		if($res) $this->redirect('showCourseSectionPage');
		else $this->error("修改失败！",U('showCourseSectionPage'));
	}
	function upMove(){
		$index=$_GET['index']-1;
		if(!isset($_GET['id'])) $_GET['id']=1;
		$courseSectionData=M('course_section')->where(array('course_id'=>$_GET['id']))->order(array('order'))->select();
		$nowData=$courseSectionData[$index];
		$preData=$courseSectionData[$index-1];
		$tmp=$nowData['order'];
		$nowData['order']=$preData['order'];
		$preData['order']=$tmp;
		M('course_section')->save($nowData);
		M('course_section')->save($preData);
		$this->redirect('showCourseSectionPage');
	}
	function downMove(){
		$index=$_GET['index'];
		if(!isset($_GET['id'])) $_GET['id']=1;
		$courseSectionData=M('course_section')->where(array('course_id'=>$_GET['id']))->order(array('order'))->select();
		$nowData=$courseSectionData[$index];
		$preData=$courseSectionData[$index-1];
		$tmp=$nowData['order'];
		$nowData['order']=$preData['order'];
		$preData['order']=$tmp;
		M('course_section')->save($nowData);
		M('course_section')->save($preData);
		$this->redirect('showCourseSectionPage');
	}
	function addSection(){
		$name=$_GET['name'];
		$cnt=M('course_section')->count();
		$saveData['name']=$name;
		$saveData['order']=$cnt+1;
		M('course_section')->add($saveData);
		$this->redirect('showCourseSectionPage');
	}
	function showCourseSubSectionPage(){
		$courseSectionData=M('course_section')->where(array('id'=>$_GET['id']))->find();
		$courseSubSectionData=M('course_sub_section')->where(array('course_section_id'=>$_GET['id']))->select();
		$this->assign('courseSubSectionData',$courseSubSectionData);
		$this->assign('courseSectionData',$courseSectionData);
		$this->display();
	}
	function addSubSection(){
		M('course_sub_section')->add($_POST);
		$this->redirect('showCourseSubSectionPage',array('id'=>$_POST['course_section_id']));
	}
	function modifySubSectionData(){
		
		M('course_sub_section')->where(array('id'=>$_POST['id']))->save($_POST);
		$this->redirect('showCourseSubSectionPage',array('id'=>$_POST['course_section_id']));
	}
	function switchSubStatic(){
		$courseSectionData=M('course_sub_section')->where(array('id'=>$_GET['id']))->find();
		$courseSectionData['status']=(string)(1-intval($courseSectionData['status']));
		$res=M('course_sub_section')->save($courseSectionData);
		if($res) $this->redirect('showCourseSubSectionPage',array('id'=>$courseSectionData['course_section_id']));
		else $this->error("修改失败！",U('showCourseSubSectionPage',array("id"=>$courseSectionData['course_section_id'])));
	}
}