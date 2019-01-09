<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../smarty/Smarty.class.php');
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once(dirname(__FILE__) . '/../common/session_helper.php');

// if (!isset($_SESSION['views']) || $_SESSION['views'] != true) {
// 	$_SESSION['views'] = false;
// 	echo "<script>window.location='../wall/index.php?url=" . $_SERVER['PHP_SELF'] . "';</script>";
// 	die;
// }

$roundno=intval($_GET['roundno'])>0?intval($_GET['roundno']):0;
$style='meepo';
// $weixin_config_m=new M('weixin_config');
// $weixin_config=$weixin_config_m->find('1');

$shake_config_m=new M('shake_config');
$shake_config=$shake_config_m->find(1);
// echo var_export($shake_config);

$shake_toshake_m=new M('shake_toshake');
$where='roundno='.$roundno.' order by point desc limit 100';//.$shake_config['maxdisplayplayers'];
$shakeinfo=$shake_toshake_m->select($where);
$shakedata=array();

foreach($shakeinfo as $item){
	$newitem['nick_name']=pack ( 'H*', $item['nickname'] );
	// 		echo $item['point']/$maxpoint*$timepercent;
// 	$progress=ceil(($maxpoint>0?($item['point']/$maxpoint*$timepercent):1)*100);
// 	$newitem['progress']=$progress;
	$newitem['avatar']=$item['avatar'];
	$shakedata[]=$newitem;
}

$smarty = new Smarty;
$smarty->caching = false;
$apppath=str_replace(DIRECTORY_SEPARATOR.'wall', '', dirname(__FILE__));
$smarty->compile_dir = $apppath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;
// echo var_export($shake_config);
$smarty->assign('currentroundno',$roundno);
$smarty->assign('maxroudno',$shake_config['currentroundno']+1);
$smarty->assign('shakeconfig',$shake_config);
$smarty->assign('shakeinfo',$shakedata);

if($shake_config['showstyle']==1){
	$smarty->display('themes/'.$style.'/shake_result.html');
}else{
	$smarty->display('themes/'.$style.'/shake_result_balloon.html');
}

