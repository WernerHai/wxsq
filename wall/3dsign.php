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
$qd_maxid=0;
$flag_m=new M('flag');
$flag=$flag_m->select('flag=2 and status!=2 order by signorder desc limit 30');
$flag=array_reverse($flag);
include("../wall/biaoqing.php");
foreach($flag as $k=>$v){
	$v['nickname']=pack('H*', $v['nickname']);
	$v['content']=pack('H*', $v['content']);
	$v= emoji_unified_to_html(emoji_softbank_to_unified($v));
	$v['content']=biaoqing($v['content']);
	$flag[$k]=$v;
}
$qd_nums=count($flag);
$qd_maxid=$qd_nums>0?$flag[$qd_nums-1]['signorder']:0;
$threedimensional_m=new M('threedimensional');
$threedimensional=$threedimensional_m->find('1');

$smarty = new Smarty;
$smarty->caching = false;
$apppath=str_replace(DIRECTORY_SEPARATOR.'wall', '', dirname(__FILE__));
$smarty->compile_dir = $apppath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;
$smarty->assign('danmuconfig',json_encode($danmu_config));
$smarty->assign('wall_config',$wall_config);
$smarty->assign('qd_maxid',$qd_maxid);
$smarty->assign('personJson',json_encode($flag));
$smarty->assign('erweima',$weixin_config['erweima']);
$smarty->assign('threedimensional_config',$threedimensional);
$smarty->assign('signlogoimg',$wall_config['signlogoimg']);
$smarty->assign('plugs',$plugs);
$smarty->display('themes/'.$style.'/header.html');
$smarty->display('themes/'.$style.'/3dsign.html');
$smarty->display('themes/'.$style.'/footer.html');