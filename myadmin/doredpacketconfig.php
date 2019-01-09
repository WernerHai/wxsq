<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once('../common/session_helper.php');
require_once("../wall/biaoqing.php");
$action=$_GET['action'];
switch ($action){
	case 'save_rule':
		save_rule();
		break;
	case 'save_tips':
		save_tips();
		break;
	case 'save_sendname':
		save_sendname();
		break;
	case 'save_wishing':
		save_wishing();
		break;
}
function save_wishing(){
	$wishing=isset($_POST['wishing'])?strval($_POST['wishing']):'';
	$redpacket_config_m=new M('redpacket_config');
	$data=array('wishing'=>$wishing);
	
	$result=$redpacket_config_m->update('1=1', $data);
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

function save_sendname(){
	$sendname=isset($_POST['sendname'])?strval($_POST['sendname']):'';
	$redpacket_config_m=new M('redpacket_config');
	$data=array('sendname'=>$sendname);
	
	$result=$redpacket_config_m->update('1=1', $data);
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
function save_tips(){
	$tips=isset($_POST['tips'])?strval($_POST['tips']):'';
	$redpacket_config_m=new M('redpacket_config');
	$data=array('tips'=>$tips);
	
	$result=$redpacket_config_m->update('1=1', $data);
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

function save_rule(){
	$rule=isset($_POST['rule'])?strval($_POST['rule']):'';
	$redpacket_config_m=new M('redpacket_config');
	$data=array('rule'=>$rule);
	$result=$redpacket_config_m->update('1=1', $data);
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