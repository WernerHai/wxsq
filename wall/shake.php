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

$shake_config_m=new M('shake_config');
$shake_config=$shake_config_m->find(1);

$shakeinfo=array(
		'slogan_list'=>array("再大力！", "再大力,再大力！", "再大力,再大力,再大力！", "摇，大力摇", "快点摇啊，别停！", "摇啊，摇啊，摇啊", "小心手机，别飞出去伤到花花草草", "看灰机～～～"),
		'ready_time'=>3,
);
$rotate_list=array();
for($i=0,$l=$shake_config['maxround'];$i<$l;$i++){
	$rotate_list[]=array(
			'id'=>$i+1,
			'countdown'=>$shake_config['duration'],
			'status'=>$shake_config['currentroundstatus'],
			'pnum'=>$shake_config['maxdisplayplayers']
	);
}

// $query=$shake_config_m->query('select max(roundno) currentroundno from weixin_shake_toshake');
// $roundinfo=$shake_config_m->fetch_array($query);
// if(empty($roundinfo['currentroundno'])){
// 	$shake_config['currentroundno']=0;
// }else{
// 	$shake_config['currentroundno']=intval($roundinfo['currentroundno'])+1;
// }

if($shake_config['currentroundstatus']==4){
	$shake_config['currentroundno']=$shake_config['currentroundno']+1;
}


require_once(dirname(__FILE__) . '/../common/CacheFactory.php');
$cache=new CacheFactory();
$cachename='shake_status';
$cache->delete($cachename);

$shakeinfo['rotate_list']=$rotate_list;
$smarty = new Smarty;
$smarty->caching = false;
$apppath=str_replace(DIRECTORY_SEPARATOR.'wall', '', dirname(__FILE__));
$smarty->compile_dir = $apppath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;

$smarty->assign('danmuconfig',json_encode($danmu_config));
$smarty->assign('wall_config',$wall_config);
$smarty->assign('shakeconfig',$shake_config);
$smarty->assign('shakeinfo',json_encode($shakeinfo));
$smarty->assign('erweima',$weixin_config['erweima']);
$smarty->assign('plugs',$plugs);

$smarty->display('themes/'.$style.'/header.html');

if($shake_config['showstyle']==1){
	$smarty->display('themes/'.$style.'/shake.html');
}else{
	$smarty->display('themes/'.$style.'/shake_balloon.html');
}
$smarty->display('themes/'.$style.'/footer.html');

