<?php
require_once('../common/session_helper.php');
include(dirname(__FILE__) . '/../common/db.class.php');
if (!isset($_SESSION['views']) || $_SESSION['views'] != true) {
	return false;
}
$omid=isset($_GET['mid'])?intval($_GET['mid']):0;
$flag_m=new M('flag');
$flag=$flag_m->find('flag=2 and signorder >'.$omid.' order by id asc');
if(!empty($flag)){
	require_once 'biaoqing.php';
	$flag['nickname']=pack('H*', $flag['nickname']);
	$flag['content']=pack('H*', $flag['content']);
	$flag= emoji_unified_to_html(emoji_softbank_to_unified($flag));
	$flag['content']=biaoqing($flag['content']);
	$result=array(
			'omid'=>$omid,
			'mid'=>$flag['signorder'],
			'avatar'=>$flag['avatar'],
			'qdnums'=>$flag['signorder'],
			'nick_name'=>$flag['nickname']
	);
	$json=json_encode($result);
	echo $json;
}else{
	$result=array(
			'omid'=>$omid,
			'mid'=>$omid,
			'avatar'=>'',
			'qdnums'=>'',
			'nick_name'=>''
	);
	$json=json_encode($result);
	echo $json;
}
return;
