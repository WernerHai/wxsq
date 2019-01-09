<?php
//幸运号码
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
//幸运号码 配置信息
$xingyunhaomaconfig_m=new M('xingyunhaoma_config');
$xingyunhaomaconfig=$xingyunhaomaconfig_m->find('1 limit 1');
$smarty = new Smarty;
$smarty->caching = false;
$apppath=str_replace(DIRECTORY_SEPARATOR.'wall', '', dirname(__FILE__));
$smarty->compile_dir = $apppath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;
$smarty->assign('wall_config',$wall_config);
$smarty->assign('danmuconfig',json_encode($danmu_config));
$smarty->assign('erweima',$weixin_config['erweima']);
$smarty->assign('plugs',$plugs);
$smarty->assign('xingyunhaomaconfig',$xingyunhaomaconfig);
$smarty->display('themes/'.$style.'/header.html');
$smarty->display('themes/'.$style.'/xyh.html');
$smarty->display('themes/'.$style.'/footer.html');