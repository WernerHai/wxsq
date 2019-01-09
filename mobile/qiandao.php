<?php
require_once('common.php');

$plugs_m=new M('plugs');
$plugs=$plugs_m->select(' switch = 1 order by ordernum');

$openid=$_GET['rentopenid'];
$flag_m=new M('flag');
$myinfo=$flag_m->find('rentopenid="'.$openid.'"');
// $controls=array('qd','wall','vote','lottory','shake','redpack');
//模版页面相关内容
require_once('../smarty/Smarty.class.php');
$smarty = new Smarty;
$smarty->debugging = false;
$smarty->caching = false;
$smarty->compile_dir = COMPILEPATH;//APPPATH.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;
$smarty->assign('title','签到');
if($myinfo['flag']==1){//没有签到
	$fromurl=isset($_GET['fromurl'])?strval($_GET['fromurl']):'';

	$wall_config=getWallConf();
	$weixin_config=getWeixinConf();
	
	$smarty->assign('wall_config',$wall_config);
	$smarty->assign('openid',$openid);
	$smarty->assign('user',$myinfo);

	$system_config_m = new M('system_config');
	$data=$system_config_m->find('configkey="mobileqiandaobg"');
	$mobileqiandaobg=GetAttachmentById(intval($data['configvalue']));

	$mobileqiandaobg=empty($mobileqiandaobg['filepath'])?'template/app/images/bg.jpg':$mobileqiandaobg['filepath'];
	$smarty->assign('mobileqiandaobg',$mobileqiandaobg);
	
	$smarty->assign('erweima',$weixin_config['erweima']);
	$smarty->assign('plugs',$plugs);
	$smarty->assign('redirecturl',urldecode($fromurl));
	$smarty->display('template/app_header.html');
	$smarty->display('template/app_register.html');
	$smarty->display('template/app_footer.html');
}else{//完成签到
	$fromurl=isset($_GET['fromurl'])?strval($_GET['fromurl']):'';
	if(!empty($fromurl)){
		header('location:'.urldecode($fromurl));
		return;
	}

	$weixin_config=getWeixinConf();

	$smarty->assign('erweima',$weixin_config['erweima']);
	$myinfo['nickname']=pack('H*', $myinfo['nickname']);
	$myinfo['datetime']=date('Y-m-d H:i:s',$myinfo['datetime']);
	$smarty->assign('openid',$openid);
	$smarty->assign('user',$myinfo);
	$smarty->assign('plugs',$plugs);
	$smarty->display('template/app_header.html');
	$smarty->display('template/app_qd.html');
}



