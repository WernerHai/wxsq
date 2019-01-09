<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once('../common/session_helper.php');
$action=$_GET['action'];
switch ($action){
	case 'setcustombg':
		setcustombg();
		break;
	case 'setlogo':
		setlogo();
		break;
	case 'setbottomlogo':
		setbottomlogo();
		break;
	case 'seterweima':
		seterweima();
		break;
	case 'setwelcometext1':
		setwelcometext1();
		break;
	case 'setwelcometext2':
		setwelcometext2();
		break;
	case 'qrcodetoptext':
		setqrcodetoptext();
		break;
}

//设置自定义背景
function setcustombg(){
	if (!empty($_FILES['custombg']['type'])) {
		//上传的文件
		require_once('../common/FileUploadFactory.php');
		$fuf=new FileUploadFactory(SAVEFILEMODE);
		$file=$fuf->SaveFormFile($_FILES['custombg']);
		
		$wall_config_m = new M('wall_config');
		$data=array('bgimg'=>$file['id']);
		$save = $wall_config_m->update('1', $data);
	}else{
		$wall_config_m = new M('wall_config');
		$data=array('bgimg'=>0);
		$save = $wall_config_m->update('1', $data);
	}
	echo "<script>alert('微信墙自定义主题背景已经更换成功！');history.go(-1);</script>";
}

//设置logo
function setlogo(){
	if (!empty($_FILES['logo']['type'])) {
		//上传文件
		/*** example usage ***/
		$filename = $_FILES['logo']['name'];
		$info = pathinfo($filename);
		require_once('../common/FileUploadFactory.php');
		$fuf=new FileUploadFactory(SAVEFILEMODE);
		$file=$fuf->SaveFormFile($_FILES['logo']);
		
		$wall_config_m = new M('wall_config');
		$data=array('logoimg'=>$file['id']);
		$save = $wall_config_m->update('1', $data);
	}else{
		$wall_config_m = new M('wall_config');
		$data=array('logoimg'=>0);
		$save = $wall_config_m->update('1', $data);
	}
	
	echo "<script>alert('微信墙顶部Logo已经配置成功！');history.go(-1);</script>";
}
function setbottomlogo(){
	if (!empty($_FILES['bottomlogo']['type'])) {
		//上传文件
		/*** example usage ***/
		$filename = $_FILES['bottomlogo']['name'];
		$info = pathinfo($filename);
		require_once('../common/FileUploadFactory.php');
		$fuf=new FileUploadFactory(SAVEFILEMODE);
		$file=$fuf->SaveFormFile($_FILES['bottomlogo']);
		
		$wall_config_m = new M('wall_config');
		$data=array('bottom_logoimg'=>$file['id']);
		$save = $wall_config_m->update('1', $data);
	}else{
		$wall_config_m = new M('wall_config');
		$data=array('bottom_logoimg'=>0);
		$save = $wall_config_m->update('1', $data);
	}
	
	echo "<script>alert('微信墙底部Logo已经配置成功！');history.go(-1);</script>";
}

function seterweima(){
	if (!empty($_FILES['erweima']['type'])) {
		if ("image/jpeg" != $_FILES['erweima']["type"] && "image/png" != $_FILES['erweima']["type"]) {
			echo "不能上传该文件格式";
			exit;
		}
		//上传的文件
		require_once('../common/FileUploadFactory.php');
		$fuf=new FileUploadFactory(SAVEFILEMODE);
		$file=$fuf->SaveFormFile($_FILES['erweima']);
		$weixin_config_m = new M('weixin_config');
		$data=array('erweima'=>$file['id']);
		$save = $weixin_config_m->update('1', $data);
	}else{
		$weixin_config_m= new M('weixin_config');
		$data=array('erweima'=>0);
		$save = $weixin_config_m->update('1', $data);
	}
	echo "<script>alert('二维码已经配置成功！');history.go(-1);</script>";
}
function setwelcometext1(){
	$text=isset($_POST['welcometext1'])?strval($_POST['welcometext1']):'';
	$wall_config_m = new M('wall_config');
	$data=array('welcometext1'=>$text);
	$result = $wall_config_m->update('1', $data);
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

function setwelcometext2(){
	$text=isset($_POST['welcometext2'])?strval($_POST['welcometext2']):'';
	$wall_config_m = new M('wall_config');
	$data=array('welcometext2'=>$text);
	$result = $wall_config_m->update('1', $data);
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
function setqrcodetoptext(){
	$text=isset($_POST['qrcodetoptext'])?strval($_POST['qrcodetoptext']):'';
	$wall_config_m = new M('wall_config');
	$data=array('qrcodetoptext'=>$text);
	$result = $wall_config_m->update('1', $data);
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