<?php
require_once(dirname(__FILE__) . '/../common/function.php');
require_once(dirname(__FILE__) . '/../common/http_helper.php');
include(dirname(__FILE__) . '/../common/db.class.php');
require_once(dirname(__FILE__) . '/../common/Attachment_helper.php');
require_once(dirname(__FILE__) . '/../common/function.php');

include('biaoqing.php');
$action = $_GET['action'];

$flag_m=new M('flag');
$wall_config= getWallConf();
switch($action){
	case 'start':
		gamestart();
		break;
	case 'end':
		gameover();
		break;
	case 'redpacket_activity_screen_record':
		redpacket_activity_screen_record();
		break;
	case 'redpacket_users':
		redpacket_users();
		break;
	case 'redpacke_zjlist':
		redpacke_zjlist();
		break;
	case 'sendingredpacket':
		ajax_act_sending_redpacket();
		break;
}

function gamestart(){
	$roundid=isset($_POST['roundid'])?intval($_POST['roundid']):0;
	$redpacket_round_m=new M('redpacket_round');
	$redpacket_round=$redpacket_round_m->find('id='.$roundid);
	if(empty($redpacket_round)){
		$data=array('errno'=>-1,"message"=>'没有可以进行的活动了');
		echo json_encode($data);
		return;
	}
	if($redpacket_round['status']==1){
		//活动未开始
		$started_at=time();
		$newdata=array('started_at'=>$started_at,'status'=>2);
		$result=$redpacket_round_m->update('id='.$roundid,$newdata);
		$data=array('errno'=>1,"message"=>"活动未开始","lefttime"=>$redpacket_round['lefttime']);
		echo json_encode($data);
		return;
	}
	if($redpacket_round['status']==2){
		//活动进行中
		$lefttime=time()-$redpacket_round['started_at'];
		$lefttime= $redpacket_round['lefttime']-$lefttime;
		$lefttime=$lefttime<=0?0:$lefttime;
		$data=array('errno'=>2,"message"=>"活动进行中","lefttime"=>$lefttime);
		echo json_encode($data);
		return;
	}
	if($redpacket_round['status']==3){
		//活动已经结束
		$data=array('errno'=>-2,"message"=>'本轮活动已经结束');
		echo json_encode($data);
		return;
	}
}

function gameover(){
	$roundid=isset($_POST['roundid'])?intval($_POST['roundid']):0;
	$redpacket_round_m=new M('redpacket_round');
	$redpacket_round=$redpacket_round_m->find('id='.$roundid);
	if(empty($redpacket_round)){
		$data=array('errno'=>-1,"message"=>'没有可以进行的活动了');
		echo json_encode($data);
		return;
	}
	if($redpacket_round['status']==1){
		$data=array('errno'=>-2,"message"=>'活动还未开始，不能结束');
		echo json_encode($data);
		return;
	}
	if($redpacket_round['status']==2){
		//活动进行中
		//检查时间
		$lefttime=time()-$redpacket_round['started_at'];
		if($lefttime>=$redpacket_round['lefttime']){
			$newdata=array('status'=>3);
			$result=$redpacket_round_m->update('id='.$roundid,$newdata);
			$data=array('errno'=>1,"message"=>'活动结束');
			echo json_encode($data);
			return;
		}else{
			$data=array('errno'=>-3,"message"=>'活动时间没到，不能结束');
			echo json_encode($data);
			return;
		}
	}
}

function redpacket_activity_screen_record(){
	$maxid=isset($_POST['max_record_id'])?intval($_POST['max_record_id']):0;
	$roundid=isset($_POST['roundid'])?intval($_POST['roundid']):0;
	$redpacket_users_m=new M('redpacket_users');
	$redpacket_users=$redpacket_users_m->select('weixin_redpacket_users.roundid='.$roundid.' and weixin_redpacket_users.userid>0 and weixin_redpacket_users.id>'.$maxid,'weixin_redpacket_users.id,weixin_flag.nickname,weixin_flag.avatar,weixin_redpacket_users.amount','','assoc','left join weixin_flag on weixin_redpacket_users.userid= weixin_flag.id');
	$redpacket_users=processzjlist($redpacket_users);
	if($redpacket_users){
		$data=array('errno'=>1,"message"=>'中奖记录','data'=>$redpacket_users);
		echo json_encode($data);
		return;
	}else{
		$data=array('errno'=>-1,"message"=>'暂时没有中奖记录');
		echo json_encode($data);
		return;
	}
}
function redpacke_zjlist(){
	
	$redpacket_users_m=new M('redpacket_users');
	$redpacket_users=$redpacket_users_m->select(' userid > 0','weixin_flag.id,weixin_flag.nickname,weixin_flag.avatar,weixin_redpacket_users.amount','','assoc','left join weixin_flag on weixin_redpacket_users.userid= weixin_flag.id');

	$redpacket_users=processzjlist($redpacket_users);
	if($redpacket_users){
		$data=array('errno'=>1,"message"=>'中奖记录','data'=>$redpacket_users);
		echo json_encode($data);
		return;
	}else{
		$data=array('errno'=>-1,"message"=>'暂时没有中奖记录');
		echo json_encode($data);
		return;
	}
}

//获取参与用户列表
function redpacket_users(){
	$maxuserid=isset($_POST['maxuserid'])?intval($_POST['maxuserid']):0;
	$flag_m=new M('flag');
	//根据签到顺序处理
	$where=' flag=2 and signorder>'.$maxuserid.' order by signorder desc limit 6';
	$flag=$flag_m->select($where);

	$redpacket_users=processuserlist($flag);
	if($redpacket_users){
		$data=array('errno'=>1,"message"=>'参数记录','data'=>$redpacket_users);
		echo json_encode($data);
		return;
	}else{
		$data=array('errno'=>-1,"message"=>'暂时没有签到记录');
		echo json_encode($data);
		return;
	}
}
function processzjlist($redpacket_users){
	$newredpacket_users=array();
	foreach($redpacket_users as $k=>$v){
		$row=array();
		$row['id']=$v['id'];
		$row['avatar']=$v['avatar'];
		$row['nick_name']=pack('H*', $v['nickname']);
		$row['money']=$v['amount']/100;
		$newredpacket_users[]=$row;
	}
// 	echo var_export($newredpacket_users);
	return $newredpacket_users;
}
function processuserlist($redpacket_users){
	$newredpacket_users=array();
	foreach($redpacket_users as $k=>$v){
		$row=array();
		$row['id']=$v['signorder'];
		$row['avatar']=$v['avatar'];
		$row['nick_name']=pack('H*', $v['nickname']);
		$newredpacket_users[]=$row;
	}
	return $newredpacket_users;
}

function ajax_act_sending_redpacket(){
	$roundid=isset($_POST['roundid'])?intval($_POST['roundid']):0;
	if($roundid<=0){
		$resultdata=array('errno'=>-1,"message"=>'轮次信息错误');
		echo json_encode($resultdata);
		return;
	}
	$redpacket_users_m=new M('redpacket_users');
	$sql='select weixin_flag.openid,b.userid,b.totalmoney from weixin_flag left join (select userid ,sum(amount) as totalmoney from weixin_redpacket_users where !isNull(userid) and isNull(updated_at) and roundid='.$roundid.' group by userid)  b on b.userid= weixin_flag.id  where weixin_flag.flag=2 and b.totalmoney>0';
	$query=$redpacket_users_m->query($sql);
	$redpacket_users=$redpacket_users_m->fetch_array($query);
	require_once '../common/redpacket_helper.php';
	$redpacket_order_return_m=new M('redpacket_order_return');
	
	$redpacket_config_m=new M('redpacket_config');
	$redpacket_config=$redpacket_config_m->find('1=1');
	$sendname=empty($redpacket_config['sendname'])?'微赢科技':$redpacket_config['sendname'];
	$wishing=empty($redpacket_config['wishing'])?'公司发财':$redpacket_config['wishing'];
	
	foreach($redpacket_users as $user){
		$returndata=sendredpacket($user['openid'],$user['totalmoney'],$sendname,$wishing);
		$redpacket_order_return_data=array(
				'return_code'=>$returndata['return_code'],
				'return_msg'=>$returndata['return_msg'],
				'result_code'=>$returndata['result_code'],
				'err_code'=>$returndata['err_code'],
				'err_code_des'=>$returndata['err_code_des'],
				'mch_billno'=>$returndata['mch_billno'],
				'mch_id'=>$returndata['mch_id'],
				'wxappid'=>$returndata['wxappid'],
				're_openid'=>$returndata['re_openid'],
				'total_amount'=>$returndata['total_amount'],
				'send_listid'=>isset($returndata['send_listid'])?$returndata['send_listid']:''
		);
		$redpacket_order_return_m->add($redpacket_order_return_data);
		if($returndata['return_code']=='SUCCESS' && $returndata['return_msg']='发放成功'){
			$newdata=array('updated_at'=>time());
			$result=$redpacket_users_m->update('userid='.$user['userid'].' and roundid='.$roundid, $newdata);
		}
	}
	$resultdata=array('errno'=>1,"message"=>'红包发放完成');
	echo json_encode($resultdata);
	return;
}









