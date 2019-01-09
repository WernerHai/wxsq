<?php 
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../smarty/Smarty.class.php');
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once(dirname(__FILE__) . '/../common/session_helper.php');
require_once(dirname(__FILE__) . '/../common/Attachment_helper.php');
require_once(dirname(__FILE__) . '/../common/function.php');

$style='meepo';

$wall_config= getWallConf();
$weixin_config= getWeixinConf();
$danmu_config=getDanmuConf();

$plugs_m=new M('plugs');
$plugs=$plugs_m->select('switch=1 order by ordernum asc');
$vote_m=new M('vote');
$vote=$vote_m->select('1 order by res desc');
// 	echo var_export($vote);
$resultdata=array();
$count=0;
foreach ($vote as $item){
	$count+=$item['res'];
}
foreach ($vote as $item){
	$newitem['id']=$item['id'];
	$newitem['name']=$item['name'];
	$newitem['res']=$item['res'];
	$newitem['percent']=floor($item['res']/$count*100);
	$resultdata[]=$newitem;
}

$smarty = new Smarty;
$smarty->caching = false;
$apppath=str_replace(DIRECTORY_SEPARATOR.'wall', '', dirname(__FILE__));
$smarty->compile_dir = $apppath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;
$smarty->assign('wall_config',$wall_config);
// $smarty->assign('qd_maxid',$qd_maxid);
// $smarty->assign('personJson',json_encode($flag));
// $smarty->assign('shakeinfo',json_encode($shakeinfo));

$smarty->assign('danmuconfig',json_encode($danmu_config));

$smarty->assign('vote_xms',$resultdata);
$smarty->assign('erweima',$weixin_config['erweima']);
// $smarty->assign('signlogoimg',$wall_config['signlogoimg']);
$smarty->assign('plugs',$plugs);
$smarty->display('themes/'.$style.'/header.html');
$smarty->display('themes/'.$style.'/vote.html');
$smarty->display('themes/'.$style.'/footer.html');

