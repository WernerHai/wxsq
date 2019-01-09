<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once(dirname(__FILE__) . '/../common/CacheFactory.php');
require_once('../common/session_helper.php');
$action=$_GET['action'];
switch ($action){
	case 'switchrentweixin':
		setrentweixin();
		break;
	case 'setappid':
		setappid();
		break;
	case 'setappsecret':
		setappsecret();
		break;
	case 'savewxpay':
		savewxpay();
		break;
}
//设置投标标题
function setrentweixin(){
	$rentweixin=isset($_POST['rentweixin'])?intval($_POST['rentweixin']):2;
	if(empty($rentweixin)){
		$resultdata=array('code'=>-2,'message'=>'参数错误');
		echo json_encode($resultdata);
		return;
	}
	$wall_config_m=new M('wall_config');
	$data=array('rentweixin'=>$rentweixin);
	$result=$wall_config_m->update('1',$data);
	if($result){
		//微信的配置写入缓存
		cachedweixinconfig();
		$resultdata=array('code'=>1,'message'=>'修改成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'修改失败');
		echo json_encode($resultdata);
		return;
	}
}
function savewxpay(){
	$mchid=isset($_POST['mchid'])?strval($_POST['mchid']):"";
	$mchsecret=isset($_POST['mchsecret'])?strval($_POST['mchsecret']):"";
	$apiclient_cert=isset($_POST['apiclient_cert'])?strval($_POST['apiclient_cert']):"";
	$apiclient_key=isset($_POST['apiclient_key'])?strval($_POST['apiclient_key']):"";
	$rootca=isset($_POST['rootca'])?strval($_POST['rootca']):"";
	require_once '../common/FileUploadFactory.php';
	$fuf=new FileUploadFactory(SAVEFILEMODE);
	$certfile=empty($apiclient_cert)?'':$fuf->SaveFile($apiclient_cert,'pem');
	$keyfile=empty($apiclient_key)?'':$fuf->SaveFile($apiclient_key,'pem');
	$rootcafile=empty($rootca)?0:$fuf->SaveFile($rootca,'pem');
	$certfile=$certfile==''?0:$certfile['id'];
	$keyfile=$keyfile==''?0:$keyfile['id'];
	$rootcafile=$rootcafile==''?0:$rootcafile['id'];
	$data=array(
			'mch_id'=>$mchid,
			'mchsecret'=>$mchsecret,
			'apiclient_cert'=>$certfile,
			'apiclient_key'=>$keyfile,
			'rootca'=>$rootcafile
	);
	$weixin_config_m=new M('weixin_config');
	$result=$weixin_config_m->update('1',$data);
	if($result){
		//微信的配置写入缓存
		cachedweixinconfig();
		$resultdata=array('code'=>1,'message'=>'修改成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'修改失败');
		echo json_encode($resultdata);
		return;
	}
}
//设置appid
function setappid(){
	$appid=isset($_POST['appid'])?strval($_POST['appid']):'';
	if(empty($appid)){
		$resultdata=array('code'=>-2,'message'=>'参数错误');
		echo json_encode($resultdata);
		return;
	}
	$weixin_config_m=new M('weixin_config');
	$data=array('appid'=>$appid);
	$result=$weixin_config_m->update('1',$data);
	if($result){
		//微信的配置写入缓存
		cachedweixinconfig();
		
		$resultdata=array('code'=>1,'message'=>'修改成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'修改失败');
		echo json_encode($resultdata);
		return;
	}
}
//设置appsecret
function setappsecret(){
	$appsecret=isset($_POST['appsecret'])?strval($_POST['appsecret']):'';
	if(empty($appsecret)){
		$resultdata=array('code'=>-2,'message'=>'参数错误');
		echo json_encode($resultdata);
		return;
	}
	$weixin_config_m=new M('weixin_config');
	$data=array('appsecret'=>$appsecret);
	$result=$weixin_config_m->update('1',$data);
	if($result){
		//微信的配置写入缓存
		cachedweixinconfig();
		$resultdata=array('code'=>1,'message'=>'修改成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'修改失败');
		echo json_encode($resultdata);
		return;
	}
}

function cachedweixinconfig(){
	//微信的配置写入缓存
	$weixin_config_m=new M('weixin_config');
	$weixin_config=$weixin_config_m->find(1);
	$cache=new CacheFactory();
	$cache->set('weixinconfig', $weixin_config, 36000*24*5);
}