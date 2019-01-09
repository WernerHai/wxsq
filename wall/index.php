<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../smarty/Smarty.class.php');
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once(dirname(__FILE__) . '/../common/http_helper.php');
require_once(dirname(__FILE__) . '/../common/session_helper.php');
require_once(dirname(__FILE__) . '/../common/Attachment_helper.php');
require_once(dirname(__FILE__) . '/../common/function.php');

$style=getparams('style');
$style = "meepo";
$wall_config= getWallConf();
$weixin_config= getWeixinConf();
$danmu_config=getDanmuConf();
$plugs_m=new M('plugs');
$plugs=$plugs_m->select('switch=1 order by ordernum asc');
$smarty = new Smarty;
$smarty->caching = false;
$apppath=str_replace(DIRECTORY_SEPARATOR.'wall', '', dirname(__FILE__));
$smarty->compile_dir = $apppath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;
$smarty->assign('wall_config',$wall_config);

$smarty->assign('title','上墙');
$smarty->assign('erweima',$weixin_config['erweima']);
$qd_nums=0;
$qd_maxid=0;
// if (isset($_SESSION['views']) && $_SESSION['views'] == true) {
$system_config_m = new M('system_config');
$showtype=$system_config_m->find('configkey="signnameshowstyle"');

$flag_m=new M('flag');
$flag=$flag_m->select('flag=2 and status!=2 order by signorder desc limit 20');
$flag=array_reverse($flag);
require_once 'biaoqing.php';
foreach($flag as $k=>$v){
	$v['nickname']=pack('H*', $v['nickname']);

    if($showtype['configvalue']==2 && !empty($v['signname'])){
        //显示姓名
        $v['nickname']=$v['signname'];
    }
    if($showtype['configvalue']==3 && !empty($v['phone'])){
        //显示电话
        $v['nickname']=substr_replace($v['phone'],'****',3,4);
    }
    

	$v['content']=pack('H*', $v['content']);
	$v= emoji_unified_to_html(emoji_softbank_to_unified($v));
	$v['content']=biaoqing($v['content']);
	$flag[$k]=$v;
}
$qd_nums=count($flag);
$qd_maxid=$qd_nums>0?$flag[$qd_nums-1]['signorder']:0;
$smarty->assign('users',$flag);
// }
// $smarty->assign('top_pannel',)
$smarty->assign('danmuconfig',json_encode($danmu_config));
$smarty->assign('qd_maxid',$qd_maxid);
$smarty->assign('qd_nums',$qd_nums);
$smarty->assign('style',$style);
$smarty->assign('lastid',getlastid());
$smarty->assign('plugs',$plugs);
$smarty->display('themes/'.$style.'/header.html');
$smarty->display('themes/'.$style.'/login.html');
$smarty->display('themes/'.$style.'/footer.html');
//获取最后一条信息的id
function getlastid(){
    $wall_m=new M('wall');
    $row=$wall_m->find(' ret=1 order by id desc limit 1','id');
    if(isset($row['id'])){
        return intval($row['id']);
    }else{
        return 0;
    }
}
?>
