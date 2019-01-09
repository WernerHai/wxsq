<?php
include(dirname(__FILE__) . '/../common/db.class.php');
require_once('../common/session_helper.php');
if (!isset($_SESSION['views']) || $_SESSION['views'] != true) {
	echo '{"ret":-1}';
	return ;
}
//用审核顺序来得到数序的排序 shenheorder
$lastshenhetime=isset($_GET['shenhetime'])?intval($_GET['shenhetime']):0;
$wall_config_m=new M('wall_config');
$wall_config=$wall_config_m->find('1');

$num=intval($wall_config['msg_historynum']);
$num=$num<=0?3:$num;
$messagelist=GetWallMessage($lastshenhetime,$num);

include("../wall/biaoqing.php");
require_once ('../common/Attachment_helper.php');
foreach($messagelist as $k=>$message){
	$message['nick_name']=pack('H*', $message['nickname']);
	unset($message['nickname']);
	$message['content']=pack('H*', $message['content']);
	//$message['content']=strip_tags($message['content']);
	$message = emoji_unified_to_html(emoji_softbank_to_unified($message));
	$message['content']=biaoqing($message['content']);
	$message['type']=1;
	if(!empty($message['image'])){
		$message['type']=2;
		$image=GetAttachmentById($message['image']);
		$message['content']=$image['filepath'];
	}
	$messagelist[$k]=$message;
}

$returndata=array();
$returndata['data']=$messagelist;
$returndata['ret']=0;
echo json_encode($returndata);
return;

function GetWallMessage($shenhetime,$limit=100){
	$wall_m=new M('wall');
	$data=$wall_m->select('shenhetime > '.$shenhetime.' and image=0 and ret=1 order by shenhetime desc limit '.$limit);
	return $data;
}
