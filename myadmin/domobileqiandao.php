<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) .'/../common/db.class.php');
require_once(dirname(__FILE__) .'/../common/session_helper.php');
$action=$_GET['action'];
switch ($action){
	case 'setmobileqiandaobg':
		setmobileqiandaobg();
		break;
}

function setmobileqiandaobg(){
	if (!empty($_FILES['mobileqiandaobg']['type'])) {
		if ("image/jpeg" != $_FILES['mobileqiandaobg']["type"] && "image/png" != $_FILES['mobileqiandaobg']["type"]) {
			echo "不能上传该文件格式";
			exit;
		}
		//上传的文件
		require_once('../common/FileUploadFactory.php');
		$fuf=new FileUploadFactory(SAVEFILEMODE);
		$file=$fuf->SaveFormFile($_FILES['mobileqiandaobg']);
		$system_config_m = new M('system_config');
		$data=array('configvalue'=>$file['id']);
		$save = $system_config_m->update('configkey="mobileqiandaobg"', $data);
	}else{
		$system_config_m = new M('system_config');
		$data=array('configvalue'=>0);
		$save = $system_config_m->update('configkey="mobileqiandaobg"', $data);
	}
	echo "<script>alert('手机签到背景图已经配置成功！');history.go(-1);</script>";
}