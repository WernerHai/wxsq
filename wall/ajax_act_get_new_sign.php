<?php
include(dirname(__FILE__) . '/../common/db.class.php');
include("../wall/biaoqing.php");
$maxid=isset($_GET['mid'])?intval($_GET['mid']):0;
$flag_m=new M('flag');

$system_config_m = new M('system_config');
$showtype=$system_config_m->find('configkey="signnameshowstyle"');

//签到名单
$signpeople=$flag_m->find('flag = 2 and status!=2 and id > '.$maxid.'  order by id asc limit 1');
if(count($signpeople)>0){
	$signpeople['nickname']=pack('H*', $signpeople['nickname']);
	if($showtype['configvalue']==2 && !empty($signpeople['signname'])){
		//显示姓名
		$signpeople['nickname']=$signpeople['signname'];
	}
	if($showtype['configvalue']==3 && !empty($signpeople['phone'])){
		//显示电话
		$signpeople['nickname']=substr_replace($signpeople['phone'],'****',3,4);
	}
		
	$signpeople['content']=pack('H*', $signpeople['content']);
	$signpeople= emoji_unified_to_html(emoji_softbank_to_unified($signpeople));
	$returndata=$signpeople;
	$returndata['error']=1;
	$returndata['mid']=$signpeople['signorder'];
	$returndata['omid']=$maxid;
// 	echo '{"error":1,"people":'.json_encode($signpeople).',"mid":'.$signpeople[0]['id'].'}';
	echo json_encode($returndata);
}else{
	echo '{"error":-1}';
}