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

$flag_m=new M('flag');
$flag_count=$flag_m->find('flag=2','*','count');
$flags=$flag_m->select('flag=2 order by signorder desc limit 6');
// echo var_export($flag_count);
$plugs_m=new M('plugs');
$plugs=$plugs_m->select('switch=1 order by ordernum asc');

$redpacket_round_m=new M('redpacket_round');
//活动从前到后顺序进行
$currentredpacket_round=$redpacket_round_m->find('status<3 order by id asc limit 1');
// echo var_export($currentredpacket_round);
$smarty = new Smarty;
$smarty->caching = false;
$apppath=str_replace(DIRECTORY_SEPARATOR.'wall', '', dirname(__FILE__));
$smarty->compile_dir = $apppath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;

$smarty->assign('danmuconfig',json_encode($danmu_config));
$smarty->assign('wall_config',$wall_config);
$smarty->assign('currentredpacket_round',$currentredpacket_round);
$smarty->assign('erweima',$weixin_config['erweima']);
$smarty->assign('flag_count',$flag_count);
$smarty->assign('flags',$flags);
$smarty->assign('plugs',$plugs);
$smarty->display('themes/'.$style.'/header.html');
$smarty->display('themes/'.$style.'/redpacket.html');
$smarty->display('themes/'.$style.'/footer.html');