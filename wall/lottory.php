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
$award_m=new M('award');
$awardlist=$award_m->select(' isdel = 1');
foreach($awardlist as $k=>$v){
	if($v['imagepath']>0){
		$img=GetAttachmentById($v['imagepath']);
		$awardlist[$k]['imagepath']=$img['filepath'];
	}else{
		$awardlist[$k]['imagepath']='/wall/themes/meepo/assets/images/defaultaward.jpg';
	}
}
$smarty = new Smarty;
$smarty->caching = false;
$apppath=str_replace(DIRECTORY_SEPARATOR.'wall', '', dirname(__FILE__));
$smarty->compile_dir = $apppath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;
$smarty->assign('danmuconfig',json_encode($danmu_config));
$smarty->assign('wall_config',$wall_config);
$smarty->assign('awardlist',$awardlist);
$smarty->assign('erweima',$weixin_config['erweima']);
$smarty->assign('plugs',$plugs);
$smarty->display('themes/'.$style.'/header.html');
$smarty->display('themes/'.$style.'/lottory.html');
$smarty->display('themes/'.$style.'/footer.html');