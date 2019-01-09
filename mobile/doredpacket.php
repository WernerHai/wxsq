<?php
error_reporting ( E_ALL );
require_once ('../common/db.class.php');
$action = $_GET ['action'];
switch ($action) {
	case 'redpacket_start' :
		redpacket_start();
		break;
	case 'redpacket_result' :
		redpacket_result();
		break;
	case 'redpacket_open':
		redpacket_open();
		break;

}
function redpacket_open(){
	$roundid=isset($_POST['roundid'])?intval($_POST['roundid']):0;
	$openid=isset($_POST['openid'])?strval($_POST['openid']):'';
	if($roundid==0){
		$data=array('errno'=>-7,"message"=>'数据异常，无法获取活动数据');
		echo json_encode($data);
		return;
	}
	if($openid==''){
		$data=array('errno'=>-8,"message"=>'数据异常，无法获取活动数据');
		echo json_encode($data);
		return;
	}
	$flag_m=new M('flag');
	$flag=$flag_m->find('openid="'.$openid.'" limit 1');
	
	$redpacket_round_m=new M('redpacket_round');
	$redpacket_round=$redpacket_round_m->find('id='.$roundid);
	$redpacket_round['chance']=$redpacket_round['chance']<1?0:$redpacket_round['chance'];
	$redpacket_round['chance']=$redpacket_round['chance']>1000?1000:$redpacket_round['chance'];
	$redpacket_users_m=new M('redpacket_users');
	$redpacket_user_count=$redpacket_users_m->find(' userid='.$flag['id'],'*','count');
	if($redpacket_round['status']==3){
		$data=array('errno'=>-1,"message"=>'本轮红包活动已经结束');
		echo json_encode($data);
		return;
	}
	if(intval($redpacket_user_count)>=intval($redpacket_round['numperperson'])){
		$data=array('errno'=>-4,"message"=>'您没有中奖');
		echo json_encode($data);
		return;
	}
	if(rand(1,1000)<=$redpacket_round['chance']){//中奖
		$record=$redpacket_users_m->find(' userid is NULL and roundid='.$roundid.' order by id asc limit 1 ');
// 		if($result){
		$newdata=array('userid'=>$flag['id']);
		$result=$redpacket_users_m->update('id='.$record['id'], $newdata);
		$data=array('errno'=>1,"message"=>'您中奖了',"data"=>array('money'=>$record['amount']/100,'zzs'=>''));
		echo json_encode($data);
		return;
		
			
	}else{
		//未中奖
		$data=array('errno'=>-6,"message"=>'没有中奖');
		echo json_encode($data);
		return;
	}
	
}

function redpacket_result(){
	$openid=isset($_POST['openid'])?strval($_POST['openid']):'';
	if($openid==''){
		$data=array('errno'=>-8,"message"=>'数据异常，无法获取活动数据');
		echo json_encode($data);
		return;
	}
	$flag_m=new M('flag');
	$flag=$flag_m->find('openid="'.$openid.'" limit 1');
	$redpacket_users_m=new M('redpacket_users');
	$redpacket_users=$redpacket_users_m->select('userid='.$flag['id']);
	if($redpacket_users){
		$redpacket_users=processzjlist($redpacket_users);
		$data=array('errno'=>1,"message"=>'中奖数据',"data"=>$redpacket_users);
		echo json_encode($data);
		return;
	}else{
		$data=array('errno'=>-1,"message"=>'暂时无人中奖');
		echo json_encode($data);
		return;
	}
}

function processzjlist($redpacket_users){

	$newredpacket_users=array('num'=>0,'money'=>0);
	foreach($redpacket_users as $k=>$v){
		$newredpacket_users['num']++;
		$newredpacket_users['money']+=($v['amount']/100);
		// $row=array();
		// $row['id']=$v['id'];
		// $row['avatar']=$v['avatar'];
		// $row['nick_name']=pack('H*', $v['nickname']);
		// $row['money']=$v['amount']/100;
		// $newredpacket_users[]=$row;
	}

	// 	echo var_export($newredpacket_users);
	return $newredpacket_users;
}
function redpacket_start(){
	$roundid=isset($_POST['roundid'])?intval($_POST['roundid']):0;
	if($roundid==0){
		$data=array('errno'=>-3,"message"=>'数据异常，无法获取活动数据');
		echo json_encode($data);
		return;
	}
	$redpacket_round_m=new M('redpacket_round');
	$redpacket_round=$redpacket_round_m->find('id='.$roundid);
	if($redpacket_round['status']==3){
		$data=array('errno'=>-2,"message"=>'活动已经结束');
		echo json_encode($data);
		return;
	}
	if($redpacket_round['status']==1){
		$data=array('errno'=>-1,"message"=>'游戏还没开始,请等待!');
		echo json_encode($data);
		return;
	}
	$lefttime=time()-$redpacket_round['started_at'];
	$lefttime=$redpacket_round['lefttime']-$lefttime;
	$lefttime=$lefttime<=0?0:$lefttime;
	$data=array('errno'=>1,"message"=>'游戏中',"lefttime"=>$lefttime);
// 	$data=array('errno'=>1,"message"=>'游戏中',"lefttime"=>30);
	echo json_encode($data);
	return;
}