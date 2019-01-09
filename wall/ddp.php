<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../smarty/Smarty.class.php');
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once(dirname(__FILE__) . '/../common/session_helper.php');
require_once(dirname(__FILE__) . '/../common/Attachment_helper.php');
require_once(dirname(__FILE__) . '/../common/function.php');
// if (!isset($_SESSION['views']) || $_SESSION['views'] != true) {
// 	$_SESSION['views'] = false;
// 	echo "<script>window.location='../wall/index.php?url=" . $_SERVER['PHP_SELF'] . "';</script>";
// 	die;
// }
$style='meepo';
$wall_config= getWallConf();
$weixin_config= getWeixinConf();
$danmu_config=getDanmuConf();
$plugs_m=new M('plugs');
$plugs=$plugs_m->select('switch=1 order by ordernum asc');

$flag_m=new M('flag');
$flag=$flag_m->select(' status<>2 ');
$womenlist=array();
$menlist=array();
foreach($flag as $item){
// 	$newitem=array();
	if($item['sex']==2){//女
// 		$newitem['id']=''
		$womenlist[]=formatpersonitem($item);
	}else{//男
		$menlist[]=formatpersonitem($item);
	}
// 	$personlist[]['mobile']=$item['phone'];
}
// echo var_export($menlist);
$award_m=new M('award');
$awardlist=$award_m->select(' isdel = 1');
$smarty = new Smarty;
$smarty->caching = false;
$apppath=str_replace(DIRECTORY_SEPARATOR.'wall', '', dirname(__FILE__));
$smarty->compile_dir = $apppath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;
$smarty->assign('danmuconfig',json_encode($danmu_config));
$smarty->assign('wall_config',$wall_config);
$smarty->assign('women',$womenlist);
$smarty->assign('men',$menlist);
// $smarty->assign('personJson',json_encode($personlist));
$smarty->assign('erweima',$weixin_config['erweima']);
// $smarty->assign('awardlist',$awardlist);
// $smarty->assign('signlogoimg',$wall_config['signlogoimg']);
$smarty->assign('plugs',$plugs);
$smarty->display('themes/'.$style.'/header.html');
$smarty->display('themes/'.$style.'/ddp.html');
$smarty->display('themes/'.$style.'/footer.html');
function formatpersonitem($person){
	$newperson=array();
	$newperson['id']=$person['id'];
	$newperson['avatar']=$person['avatar'];
	$newperson['nick_name']=pack('H*', $person['nickname']);//$person['nickname'];
	return $newperson;
}