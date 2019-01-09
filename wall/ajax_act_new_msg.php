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

$system_config_m = new M('system_config');
$showtype=$system_config_m->find('configkey="wallnameshowstyle"');


$num=intval($wall_config['msg_historynum']);
$num=$num<=0?3:$num;

$messagelist=GetWallMessage($lastshenhetime,$num);

include("../wall/biaoqing.php");
require_once ('../common/Attachment_helper.php');
foreach($messagelist as $k=>$message){
	$message['nick_name']=pack('H*', $message['nickname']);

	if($showtype['configvalue']==2 && !empty($message['signname'])){
		//显示姓名
		$message['nick_name']=$message['signname'];
	}
	if($showtype['configvalue']==3 && !empty($message['phone'])){
		//显示电话
		$message['nick_name']=substr_replace($message['phone'],'****',3,4);
	}
	unset($message['signname']);
	unset($message['phone']);
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
	//select($where = '1', $field = "*", $fun = '', $type = 'assoc',$join='')
	$data=$wall_m->select('shenhetime > '.$shenhetime.' and ret=1 order by shenhetime desc limit '.$limit,'weixin_wall.nickname,weixin_wall.avatar,weixin_wall.content,weixin_wall.image,weixin_flag.phone,weixin_flag.signname,weixin_wall.shenhetime','','','left join weixin_flag on weixin_flag.openid=weixin_wall.openid');
	// echo var_export($data);
	// exit();
	return $data;
}
