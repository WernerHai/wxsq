<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once('../common/session_helper.php');
require_once("../wall/biaoqing.php");
$action=$_GET['action'];
switch ($action){
	case 'setdesignated':
		setdesignated();
		break;
	case 'setband':
		setband();
		break;

	case 'getdesignated':
		getdesignated();
		break;
}

//设置内定
function setdesignated(){
	$openid=isset($_POST['openid'])?strval($_POST['openid']):'';
	$awardid=isset($_POST['awardid'])?intval($_POST['awardid']):0;
	$designated=isset($_POST['designated'])?intval($_POST['designated']):0;
	$from=isset($_POST['from'])?strval($_POST['from']):'';
	if($openid==''){
		echo '{"code":-1,"message":"用户openid格式不正确"}';
		return ;
	}
	if($awardid==0){
		echo '{"code":-2,"message":"奖品id不正确"}';
		return ;
	}
	if($from==''){
		echo '{"code":-3,"message":"没有选择活动类型"}';
		return ;
	}
	if($designated==0){
		//echo '{"errno":-3,"message":"内定状态不正确"}';
		return ;
	}
	$zjlist_m=new M('zjlist');
	$where='openid="'.$openid.'" and awardid='.$awardid;
	$zjlist=$zjlist_m->find($where);
	if(!empty($zjlist)){
		//修改现有的内定设置
		$data=array('designated'=>$designated);
		$result=$zjlist_m->update($where,$data);
		if($result){
			echo '{"code":1,"message":"内定设置修改成功"}';
			return ;
		}else{
			echo '{"code":-4,"message":"内定设置修改失败"}';
			return ;
		}
	}else{
		if($designated==1){
			return ;
		}
		//新增内定设置
		$data=array('openid'=>$openid,'awardid'=>$awardid,'designated'=>$designated,'status'=>1,'verifycode'=>'','fromplug'=>$from);
		$result=$zjlist_m->add($data);
		if($result){
			echo '{"code":2,"message":"内定设置成功"}';
			return ;
		}else{
			echo '{"code":-5,"message":"内定设置失败"}';
			return ;
		}
	}
}
//获取已有的内定设置
function getdesignated(){
	$openid=isset($_POST['openid'])?strval($_POST['openid']):'';
	$view_zjlist_m=new M('view_zjlist');
	$where='openid="'.$openid.'" and designated>1';// and awardid='.$awardid;
	$view_zjlist=$view_zjlist_m->select($where);
	
	
	if(empty($view_zjlist)){
		echo '{"code":0,"message":"没有数据"}';
		return;
	}
	
	$plugs_m=new M('plugs');
	$cjplugs=$plugs_m->select('choujiang>0 order by ordernum desc');
	$plugs=array();
	
	foreach($cjplugs as $v){
		$plugs[$v['name']]=$v['title'];
	}
	foreach($view_zjlist as $k=>$v){
		$view_zjlist[$k]['cjtype']=$plugs[$v['fromplug']];
	}
	
	$data=array(
			"code"=>1,
			"message"=>"",
			"data"=>$view_zjlist
	);
	echo json_encode($data);
	return;
}
//设置禁用
function setband(){
	$openid=isset($_POST['openid'])?strval($_POST['openid']):'';
	$status=isset($_POST['status'])?intval($_POST['status']):0;
	if($openid==''){
		echo '{"code":-1,"message":"用户openid格式不正确"}';
		return ;
	}
	if($status==0){
		echo '{"code":-2,"message":"设置的状态不正确"}';
		return ;
	}
	
	$flag_m=new M('flag');
	$where='openid="'.$openid.'"';
	$data=array('status'=>$status);
	$result=$flag_m->update($where,$data);
	if($result){
		echo '{"code":1,"message":"用户状态修改成功"}';
		return ;
	}else{
		echo '{"code":-3,"message":"设用户状态修改失败"}';
		return ;
	}
}