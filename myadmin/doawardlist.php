<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once(dirname(__FILE__) . '/../common/session_helper.php');
require_once(dirname(__FILE__) . '/../common/Attachment_helper.php');
$action=$_GET['action'];
switch ($action){
	case 'save':
		saveaward();
		break;
	case 'get':
		getaward();
		break;
	case 'del':
		delaward();
		break;
}
function saveaward(){
	$id=isset($_POST['id'])?intval($_POST['id']):0;
	$awardname=isset($_POST['awardname'])?strval($_POST['awardname']):'';
	if($awardname==''){
		$resultdata=array('code'=>-2,'message'=>'奖品名称必须填写');
		echo json_encode($resultdata);
		return;
	}

	//添加
	$awardinfo=array();
	$awardinfo['awardname']=$awardname;
	
	if (!empty($_FILES['imagepath']['type'])) {
		//上传的文件
		require_once('../common/FileUploadFactory.php');
		$fuf=new FileUploadFactory(SAVEFILEMODE);
		$file=$fuf->SaveFormFile($_FILES['imagepath']);
		$awardinfo['imagepath']=$file['id'];
	}
	$award_m=new M('award');
	$awardinfo['created_at']=time();
	if($id>0){
		$result=$award_m->update('id='.$id,$awardinfo);
	}else{
		$result=$award_m->add($awardinfo);
	}
	if($result){
		$resultdata=array('code'=>1,'message'=>'保存成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'保存失败');
		echo json_encode($resultdata);
		return;
	}
}

function getaward(){
	$id=isset($_POST['id'])?intval($_POST['id']):0;
	if($id<=0){
		$resultdata=array('code'=>-2,'message'=>'ID错误');
		echo json_encode($resultdata);
		return;
	}
	$award_m=new M('award');
	$awardinfo=$award_m->find('id='.$id);
	$image=GetAttachmentById($awardinfo['imagepath']);
	$awardinfo['imagepath']=$image['filepath'];
	$resultdata=array('code'=>1,"data"=>$awardinfo);
	echo json_encode($resultdata);
	return;
}

function delaward(){
	$id=isset($_POST['id'])?intval($_POST['id']):0;
	if($id<=0){
		$resultdata=array('code'=>-2,'message'=>'ID错误');
		echo json_encode($resultdata);
		return;
	}

	
	$data=array('isdel'=>2);
	$award_m=new M('award');
	$result=$award_m->update('id='.$id,$data);
	
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