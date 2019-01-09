<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once('../common/session_helper.php');
$action=$_GET['action'];
switch ($action){
	case 'fj':
		fajiang();
		break;
	case 'del':
		delzj();
		break;
	case 'delzjdesignated':
		delzjdesignated();
		break;
}
//发奖
function fajiang(){
	$openid=isset($_POST['openid'])?strval($_POST['openid']):'';
	$currentplug=isset($_POST['plug'])?strval($_POST['plug']):'';
	if(empty($openid) || empty($currentplug)){
		$resultdata=array('code'=>-2,'message'=>'数据错误');
		echo json_encode($resultdata);
		return;
	}
	$zjlist_m=new M('zjlist');
	$data=array('status'=>3,'fjdatetime'=>time());
	$result=$zjlist_m->update('openid="'.$openid.'" and fromplug="'.$currentplug.'"',$data);
	if($result){
		$resultdata=array('code'=>1,'message'=>'发奖成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'发奖失败');
		echo json_encode($resultdata);
		return;
	}
	
}
//删除中奖信息
function delzj(){
	$openid=isset($_POST['openid'])?strval($_POST['openid']):'';
	$currentplug=isset($_POST['plug'])?strval($_POST['plug']):'';
	if(empty($openid) || empty($currentplug)){
		$resultdata=array('code'=>-2,'message'=>'数据错误');
		echo json_encode($resultdata);
		return;
	}
	$zjlist_m=new M('zjlist');
	$where='openid="'.$openid.'" and fromplug="'.$currentplug.'"';
	$data=$zjlist_m->find($where);
	$result=false;
	if($data['designated']==2 || $data['designated']==3){
		$result=$zjlist_m->update($where,'status=1');
	}else{
		$result=$zjlist_m->delete($where);
	}
	
	if($result){
		$resultdata=array('code'=>1,'message'=>'删除成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'删除失败');
		echo json_encode($resultdata);
		return;
	}
}


//删除中奖信息
function delzjdesignated(){
	$openid=isset($_POST['openid'])?strval($_POST['openid']):'';
	$currentplug=isset($_POST['plug'])?strval($_POST['plug']):'';
	if(empty($openid) || empty($currentplug)){
		$resultdata=array('code'=>-2,'message'=>'数据错误');
		echo json_encode($resultdata);
		return;
	}
	$zjlist_m=new M('zjlist');
	$where='openid="'.$openid.'" and fromplug="'.$currentplug.'"';
	$result=$zjlist_m->delete($where);
	if($result){
		$resultdata=array('code'=>1,'message'=>'删除成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'删除失败');
		echo json_encode($resultdata);
		return;
	}
}