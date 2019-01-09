<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once('../common/session_helper.php');
$action=$_GET['action'];
switch ($action){
	case 'switchplugs':
		switchplugs();
		break;
	case 'changescreenpaw':
		changescreenpaw();
		break;
	case 'cleardata':
		cleardata();
		break;
	case 'copyright':
		copyright();
		break;
	case 'copyrightlink':
		copyrightlink();
		break;
	case 'switchname':
		switchname();
		break;
	case 'switchphone':
		switchphone();
		break;
	case 'changepwd':
		changepwd();
		break;
	case 'resetmobileurl':
		resetmobileurl();
		break;
}

function resetmobileurl(){
	$wall_config_m=new M('wall_config');
	$verifycode=uniqid();
	$data=array('verifycode'=>$verifycode);
	$result=$wall_config_m->update('1',$data);
	if($result){
		$resultdata=array('code'=>1,'message'=>'修改成功','vcode'=>$verifycode);
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'修改失败');
		echo json_encode($resultdata);
		return;
	}
}

function switchname(){
	$status=isset($_POST['status'])?intval($_POST['status']):1;
	
	$wall_config_m=new M('wall_config');
	$data=array('name_switch'=>$status);
	$result=$wall_config_m->update('1',$data);
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

function switchphone(){
	$status=isset($_POST['status'])?intval($_POST['status']):1;
	$wall_config_m=new M('wall_config');
	$data=array('phone_switch'=>$status);
	$result=$wall_config_m->update('1',$data);
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

//各个功能的开关
function switchplugs(){
	$plugname=isset($_POST['name'])?strval($_POST['name']):'';
	if($plugname==''){
		$resultdata=array('code'=>-2,'message'=>'数据错误');
		echo json_encode($resultdata);
		return;
	}
	$switchstatus=isset($_POST['switchstatus'])?intval($_POST['switchstatus']):0;
	$plugs_m=new M('plugs');
	$data=array('switch'=>$switchstatus);
	$result=$plugs_m->update('name="'.$plugname.'"',$data);
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
//修改开场密码
function changescreenpaw(){
	$screenpaw=isset($_POST['screenpaw'])?$_POST['screenpaw']:'';
	if($screenpaw==''){
		$resultdata=array('code'=>-1,'message'=>'开场密码不能为空');
		echo json_encode($resultdata);
		return;
	}
	$wall_config_m=new M('wall_config');
	$data=array('screenpaw'=>$screenpaw);
	$result=$wall_config_m->update('1',$data);
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
//清空上墙系统的所有数据，包括 签到用户信息，上墙的消息，摇一摇数据，中奖名单
function cleardata(){
	$wall_config_m=new M('wall_config');
	//清空签到数据
	$wall_config_m->query('truncate table weixin_flag');
	//清空上墙消息
	$wall_config_m->query('truncate table weixin_wall');
	//清空投票票数
	$wall_config_m->query('update weixin_vote set res=0 where 1');
	//重置摇一摇状态
	$wall_config_m->query('update weixin_shake_config set currentroundno=0,currentroundstatus=1 where 1');
	//清空摇一摇数据
	$wall_config_m->query('truncate table weixin_shake_toshake');
	//清空中奖数据
	$wall_config_m->query('truncate table weixin_zjlist');
	//清空红包轮次数据
	$wall_config_m->query('truncate table weixin_redpacket_round');
	//清空红包中奖数据
	$wall_config_m->query('truncate table weixin_redpacket_users');
	//清空红包发送记录
	$wall_config_m->query('truncate table weixin_redpacket_orders');
	//清空红包接收记录
	$wall_config_m->query('truncate table weixin_redpacket_order_return');
	//清空幸运号码
	$wall_config_m->query('truncate table weixin_xingyunhaoma');
	//清空幸运手机号
	$wall_config_m->query('truncate table weixin_xingyunshoujihao');
	
	$resultdata=array('code'=>1,'message'=>'微信墙已经焕然一新，可以开始一场新的活动了');
	echo json_encode($resultdata);
	return;
}
//修改管理员密码
function changepwd(){
	$oldpwd=isset($_POST['oldpwd'])?strval($_POST['oldpwd']):'';
	$newpwd=isset($_POST['newpwd'])?strval($_POST['newpwd']):'';
	$validpwd=isset($_POST['validpwd'])?strval($_POST['validpwd']):'';
	if(empty($oldpwd) || empty($newpwd) || empty($validpwd)){
		$resultdata=array('code'=>-1,'message'=>'数据错误');
		echo json_encode($resultdata);
		return;
	}
	if($newpwd!=$validpwd){
		$resultdata=array('code'=>-2,'message'=>'2次输入的新密码不一致，请重新输入');
		echo json_encode($resultdata);
		return;
	}
	$admin_m=new M('admin');
	$admin=$admin_m->find('1');
	if($admin['pwd']!=$oldpwd){
		$resultdata=array('code'=>-3,'message'=>'原密码错误');
		echo json_encode($resultdata);
		return;
	}
	
	$return=$admin_m->update(' 1 ', array('pwd'=>$newpwd));
	if($return!==false){
		$resultdata=array('code'=>1,'message'=>'修改成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-4,'message'=>'修改失败');
		echo json_encode($resultdata);
		return;
	}
}


function copyright(){
	$copyright=isset($_POST['copyright'])?$_POST['copyright']:'';

	$wall_config_m=new M('wall_config');
	$data=array('copyright'=>$copyright);
	$result=$wall_config_m->update('1',$data);
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
function copyrightlink(){
	$copyrightlink=isset($_POST['copyrightlink'])?$_POST['copyrightlink']:'';

	$wall_config_m=new M('wall_config');
	$data=array('copyrightlink'=>$copyrightlink);
	$result=$wall_config_m->update('1',$data);
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

