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
$flag_m=new M('flag');
$flag=$flag_m->select('phone is not NULL and phone !="" ');
$personlist=array();
foreach($flag as $item){
	$personlist[]['mobile']=$item['phone'];
}
$award_m=new M('award');
$awardlist=$award_m->select(' isdel = 1');
$smarty = new Smarty;
$smarty->caching = false;
$apppath=str_replace(DIRECTORY_SEPARATOR.'wall', '', dirname(__FILE__));
$smarty->compile_dir = $apppath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;
// $smarty->assign('qd_maxid',$qd_maxid);
$smarty->assign('danmuconfig',json_encode($danmu_config));
$smarty->assign('wall_config',$wall_config);
$smarty->assign('personJson',json_encode($personlist));
$smarty->assign('erweima',$weixin_config['erweima']);
$smarty->assign('awardlist',$awardlist);
// $smarty->assign('signlogoimg',$wall_config['signlogoimg']);
$smarty->assign('plugs',$plugs);
$smarty->display('themes/'.$style.'/header.html');
$smarty->display('themes/'.$style.'/cjx.html');
$smarty->display('themes/'.$style.'/footer.html');