<?php
require_once('common.php');
$plugs_m=new M('plugs');
$plugs=$plugs_m->select(' switch = 1 order by ordernum');
$openid=$_GET['rentopenid'];
 $isopen=false;
foreach($plugs as $item){
	if($item['choujiang']==1){
		$isopen=true;
		break;
	}
}
if(!$isopen){
	header('location:qiandao.php?rentopenid='.$openid);
}

$flag_m=new M('flag');
$myinfo=$flag_m->find('rentopenid="'.$openid.'"');
$myinfo['nickname']=pack('H*',$myinfo['nickname']);
$zjlist_m=new M('view_zjlist');
$zjlist=$zjlist_m->select('openid="'.$openid.'" and status>1');
$newzjlist=array();
$statetext=array('未中','','中奖','已发');
$cjtext=array('cjx'=>'抽奖箱','cj'=>'抽奖','zjd'=>'砸金蛋','threedimensionallottery'=>'3D抽奖');
foreach($zjlist as $v){
	$item['fromplug']=$cjtext[$v['fromplug']];
	$item['awardname']=$v['awardname'];
	$item['zjdatetime']=date('m月d日 H:i:s',$v['zjdatetime']);
	$item['status']=$statetext[$v['status']];
	$newzjlist[]=$item;
}
//模版页面相关内容
require_once('../smarty/Smarty.class.php');
$smarty = new Smarty;
$smarty->debugging = false;
$smarty->caching = false;
$smarty->compile_dir =COMPILEPATH;//APPPATH.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;


$smarty->assign('title','中奖结果');
$smarty->assign('openid',$openid);
$smarty->assign('user',$myinfo);
$smarty->assign('zjlist',$newzjlist);

$smarty->assign('plugs',$plugs);

$weixin_config=getWeixinConf();
$smarty->assign('erweima',$weixin_config['erweima']);

$smarty->display('template/app_header.html');
$smarty->display('template/app_zjlist.html');
$smarty->display('template/app_footer.html');