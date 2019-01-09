<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once('../common/session_helper.php');
require_once("../wall/biaoqing.php");
$action=$_GET['action'];
switch ($action){
	case 'addwallnotice':
		addwallnotice();
		break;
}

function addwallnotice(){
	$message=isset($_POST['message'])?strval($_POST['message']):'';
	if(empty($message)){
		$resultdata=array('code'=>-1,'message'=>'公告内容不能为空');
		echo json_encode($resultdata);
		return;
	}
	$data=array(
		'messageid' => 0,
		'fakeid' => 123,
		'num' => -1,
		'content' => bin2hex($message),
		'nickname' => bin2hex('系统公告'),
		'avatar' => '/wall/themes/meepo/assets/images/notice.jpg',
		'ret' => 1,
		'fromtype' => 'weixin',
		'image' => 0,
		'shenhetime'=>time()
	);
	$wall_m=new M('wall');
	$result=$wall_m->add($data);
	if($result){
		$resultdata=array('code'=>1,'message'=>'发送成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-2,'message'=>'发送失败');
		echo json_encode($resultdata);
		return;
	}
}