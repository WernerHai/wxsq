<?php
require_once('common.php');

$plugs_m=new M('plugs');
$plugs=$plugs_m->select(' switch = 1 order by ordernum');

$openid=$_GET['rentopenid'];

$isopen=false;
foreach($plugs as $item){
	if($item[name]=='wall'){
		$isopen=true;
	}
}
if(!$isopen){
	header('location:qiandao.php?rentopenid='.$openid);
}

$flag_m=new M('flag');
$myinfo=$flag_m->find('rentopenid="'.$openid.'"');

//模版页面相关内容
require_once('../smarty/Smarty.class.php');
$smarty = new Smarty;
$smarty->debugging = false;
$smarty->caching = false;
$smarty->compile_dir =COMPILEPATH;//APPPATH.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;

//$controls=array('qd','wall','vote','lottory','shake','redpack');
$smarty->assign('title','上墙');
$smarty->assign('openid',$openid);
$smarty->assign('user',$myinfo);
//$smarty->assign('xianchang',array('controls'=>$controls));
$smarty->assign('plugs',$plugs);

$weixin_config=getWeixinConf();
$smarty->assign('erweima',$weixin_config['erweima']);
$smarty->display('template/app_header.html');
$smarty->display('template/app_wall.html');
$smarty->display('template/app_footer.html');