<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends BaseAction {
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
	/*显示所有用户*/
	public function showAllUserRank(){
		$where['nickname']  = array('like','%'.$sortParam.'%');
		$where['solve_problem']  = array('like','%'.$sortParam.'%');
		$where['_logic'] = 'or';
		$map['_complex'] = $where;
		$map['status']  = 1;
		
		$User = M('User'); // 实例化User对象
		import('ORG.Util.Page');// 导入分页类
		$count      = $User->where($map)->count();// 查询满足要求的总记录数
		$Page       = new Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数
		$show       = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        //order('solve_problem desc,submissions asc')
		$list = $User->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
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
		}
		//$this->assign('color','red');
		$list=array_slice($list,0,5);
		return $list;
		
	}
	public function getLadderList(){
		$list=M('ladder_contest')->order('id desc')->select();
		$list=array_slice($list,0,5);
		return $list;
	}
	public function getBulletin(){
		$bulletinData=M('bulletin')->order('creat_time desc')->select()[0]['content'];
		return $bulletinData;
	}
	public function index(){
		$courseData=M('course')->find();
		$this->assign('courseData',$courseData);
		$userList=$this->showAllUserRank();
		$this->assign('userList',$userList);
		$bulletinData=$this->getBulletin();
		$this->assign('bulletinData',$bulletinData);
		$ladderList=$this->getLadderList();
		$hotSearch=$this->getHotSearchProblem();
		//dump($hotSearch);
		$this->assign('hotSearch',$hotSearch);
		$this->assign('ladderList',$ladderList);
		$this->display();
	}
	public function getOneTypeProblem($ans,$tableName,$dayNum){
		
		$problemTable=$tableName.'problem';
		if($tableName=='ladder_') $problemTable='problem';
		$problem=M($problemTable);
		$newTime=time();
		$where['submit_time']=array('GT',time()-60*60*24*$dayNum);
		$newTableName=$tableName.'user_problem';
		$allRecord=M($newTableName)->where($where)->select();
		$problemCnt=array();
		$problemName=array();
		foreach($allRecord as $k => $v){
			$problemCnt[$allRecord[$k]['problem_id']]++;
		}
		arsort($problemCnt);
		$cnt=0;
		foreach($problemCnt as $k => $v){
			$ans[$cnt][$tableName.'problem_id']=$k;
			$ans[$cnt][$tableName.'cnt']=$v;
			$ans[$cnt][$tableName.'title']=$problem->where(array('id'=>$k))->find()['title'];
			$cnt++;
			if($cnt>=5) break;
		}
		return $ans;
	}
	public function getHotSearchProblem(){
		$ans=array();
		$ans=$this->getOneTypeProblem($ans,'',7);
		$ans=$this->getOneTypeProblem($ans,'ladder_',7);
		$ans=$this->getOneTypeProblem($ans,'train_',7);
		return $ans;
	}
	public function randomSelectProblem(){
		$userinfo=session('userinfo');
		$where['user_id']=$userinfo['id'];
		$where['judge_status']=0;
		$ladderUserProblem=M('ladder_user_problem')->where(array('user_id'=>$userinfo['id']))->select();
		foreach($ladderUserProblem as $k => $v){
			$ladderUserProblem[$k]=$ladderUserProblem[$k]['problem_id'];
		}
		//dump($ladderUserProblem);
		$condition['problem_id']=array('not in',$ladderUserProblem);
		$ladderProblem=M('ladder_contest_problem')->where($condition)->distinct(true)->field('problem_id')->select();
		
		foreach($ladderProblem as $k => $v){
			$ladderProblem[$k]=$ladderProblem[$k]['problem_id'];
		}
		//dump($ladderProblem);
		$acProblemId=M('user_problem')->where($where)->distinct(true)->field('problem_id')->select();
		foreach($acProblemId as $k =>$v){
			$acProblemId[$k]=$acProblemId[$k]['problem_id'];
		}
		$acProblemId=array_merge($acProblemId,$ladderProblem);
		//dump($acProblemId);
		//die;
		$map['id']=array('not in',$acProblemId);
		$selectProblemList=M('problem')->where($map)->field('id')->select();
		$len=count($selectProblemList);
		$pos=rand(0,$len);
		$randomProblemId=$selectProblemList[$pos]['id'];
		$this->redirect('Problem/showProblem', array('id' => $randomProblemId));
		//dump($selectProblemList);
	}
	public function jumpProblem(){
		$problem_id=$_POST['problem_id'];
		$this->redirect('Problem/showProblem', array('id' => $problem_id));
	}
	public function jumpLibrary(){
		$this->redirect('Problem/showProblem', array('id' => $_GET['id']));
	}
	public function jumpLadder(){
		$this->redirect('Ladder/showProblemPage', array('id' => $_GET['id']));
	}
	public function jumpTrain(){
		$level_msg_id=M('train_problem')->where(array('id'=>$_GET['id']))->find()['level_msg_id'];
		$level_id=M('level_msg')->where(array('id'=>$level_msg_id))->find()['level_id'];
		session('levelMsgId',$level_msg_id);
		session('levelId',$level_id);
		$this->redirect('Train/showProblem', array('id' => $_GET['id']));
	}
}