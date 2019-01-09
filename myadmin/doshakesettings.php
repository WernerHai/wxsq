<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once('../common/session_helper.php');
$action=$_GET['action'];
switch ($action){
	case 'editmaxdisplayplayers':
		editmaxdisplayplayers();
		break;
	case 'editmaxplayers':
		editmaxplayers();
		break;
	case 'editgameduration':
		editgameduration();
		break;
	case 'editgamemaxround':
		editgamemaxround();
		break;
	case 'resetgame':
		resetgame();
		break;
	case 'resetgamecurrentround':
		resetgamecurrentround();
		break;
	case 'set_showstyle':
		set_showstyle();
		break;
}
//修改最大显示人数
function editmaxdisplayplayers(){
	$maxdisplayplayers=isset($_POST['maxdisplayplayers'])?intval($_POST['maxdisplayplayers']):0;
	$shake_config_m=new M('shake_config');
	$data=array('maxdisplayplayers'=>$maxdisplayplayers);
	$result=$shake_config_m->update('1',$data);
	if($result){
		$resultdata=array('code'=>1,'message'=>'修改成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'修改失败');
		echo json_encode($resultdata);
		return;
	}
}
//设置显示方式 1是横向 2是纵向
function set_showstyle(){
	$showstyle=isset($_POST['showstyle'])?intval($_POST['showstyle']):1;
	$showstyle=$showstyle!=1?2:1;
	$shake_config_m=new M('shake_config');
	$data=array('showstyle'=>$showstyle);
	$result=$shake_config_m->update('1',$data);
	if($result){
		$resultdata=array('code'=>1,'message'=>'修改成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'修改失败');
		echo json_encode($resultdata);
		return;
	}
}

//修改最大参与人数
function editmaxplayers(){
	$maxplayers=isset($_POST['maxplayers'])?intval($_POST['maxplayers']):0;
	$shake_config_m=new M('shake_config');
	$data=array('maxplayers'=>$maxplayers);
	$result=$shake_config_m->update('1',$data);
	if($result){
		$resultdata=array('code'=>1,'message'=>'修改成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'修改失败');
		echo json_encode($resultdata);
		return;
	}
}
//修改游戏持续时间
function editgameduration(){
	$duration=isset($_POST['duration'])?intval($_POST['duration']):0;
	$shake_config_m=new M('shake_config');
	$data=array('duration'=>$duration);
	$result=$shake_config_m->update('1',$data);
	if($result){
		$resultdata=array('code'=>1,'message'=>'修改成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'修改失败');
		echo json_encode($resultdata);
		return;
	}
}
//修改最大游戏轮次
function editgamemaxround(){
	$maxround=isset($_POST['maxround'])?intval($_POST['maxround']):0;
	$shake_config_m=new M('shake_config');
	$data=array('maxround'=>$maxround);
	$result=$shake_config_m->update('1',$data);
	if($result){
		$resultdata=array('code'=>1,'message'=>'修改成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'修改失败');
		echo json_encode($resultdata);
		return;
	}
}
//重置游戏
function resetgame(){
	$shake_config_m=new M('shake_config');
	//重置状态
	$shake_config_m->update('1','currentroundstatus=1,currentroundno=0');
	$shake_config_m->query('TRUNCATE TABLE weixin_shake_toshake');
	$resultdata=array('code'=>1,'message'=>'游戏重置成功，摇一摇功能已经焕然一新');
	echo json_encode($resultdata);
	return;
}
//重置最后一轮游戏
function resetgamecurrentround(){
	$shake_config_m=new M('shake_config');
	$shake_config=$shake_config_m->find('1');
	//重置状态
	$shake_config_m->update('1','currentroundstatus=1');
	//清空最后一轮的摇晃数据
	$shake_toshake_m=new M('shake_toshake');
	$where='roundno='.$shake_config['currentroundno'];
	$shake_toshake_m->delete($where);
	
	require_once(dirname(__FILE__) . '/../common/CacheFactory.php');
	$cache=new CacheFactory(CACHEMODE);
	$cachename='shake_status';
	$status=$cache->delete($cachename);
	
	$resultdata=array('code'=>1,'message'=>'当前轮次已经重置，可以重新开始游戏了');
	echo json_encode($resultdata);
	return;
}