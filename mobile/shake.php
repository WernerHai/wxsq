<?php
require_once('common.php');
$plugs_m=new M('plugs');
$plugs=$plugs_m->select(' switch = 1 order by ordernum');
$openid=$_GET['rentopenid'];
$isopen=false;
foreach($plugs as $item){
	if($item[name]=='shake'){
		$isopen=true;
	}
}
if(!$isopen){
	header('location:qiandao.php?rentopenid='.$openid);
}
$shake_config_m=new M("shake_config");
$shake_config=$shake_config_m->find('1');
//模版页面相关内容
require_once('../smarty/Smarty.class.php');
$smarty = new Smarty;
$smarty->debugging = false;
$smarty->caching = false;
$smarty->compile_dir =COMPILEPATH;

$smarty->assign('title','摇一摇');
$smarty->assign('openid',$openid);

$weixin_config=getWeixinConf();
$smarty->assign('erweima',$weixin_config['erweima']);
$smarty->assign('shake_config',$shake_config);

$smarty->assign('plugs',$plugs);
$smarty->display('template/app_header.html');
$smarty->display('template/app_shake.html');
$smarty->display('template/app_footer.html');