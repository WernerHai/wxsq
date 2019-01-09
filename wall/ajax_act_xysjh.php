<?php

require_once(dirname(__FILE__) . '/../common/function.php');
include(dirname(__FILE__) . '/../common/db.class.php');

$action = $_GET['action'];
switch ($action){
	//获取幸运手机号
	case 'getxysjh':
		getxysjh();
		break;
	//获取之前抽中的幸运手机号
	case 'getoldxysjh':
		getoldxysjh();
		break;
}
//显示已经抽取出来幸运手机号 号码
function getoldxysjh(){
	$xingyunshoujihao_m=new M('xingyunshoujihao');
	$data=$xingyunshoujihao_m->select('weixin_xingyunshoujihao.status=2 order by weixin_xingyunshoujihao.ordernum desc','weixin_flag.phone','','assoc','left join weixin_flag on weixin_flag.openid=weixin_xingyunshoujihao.openid');
	$luckphone_arr=array();
	foreach($data as $v){
		array_push($luckphone_arr, $v['phone']);
	}
	$returndata=array('code'=>1,'luckphone_arr'=>$luckphone_arr);
	echo json_encode($returndata);
	return;
}
//获取幸运手机号码
function getxysjh(){
	$xingyunshoujihao_m=new M('xingyunshoujihao');
	$maxordernum=$xingyunshoujihao_m->find('status=2','*','count');
	//当前的获奖序号
	$currentordernum=$maxordernum+1;

	$bizhong=$xingyunshoujihao_m->find('weixin_xingyunshoujihao.ordernum='.$currentordernum.' and weixin_xingyunshoujihao.designated=2',
			'phone','','assoc',
			'left join weixin_flag on weixin_xingyunshoujihao.openid=weixin_flag.openid');
	//如果是必中的就直接返回结果
	if(!empty($bizhong)){
		$xingyunshoujihao_m->update('ordernum='.$currentordernum.' and designated=2', array('status'=>2));
		$returndata=array('code'=>1,'luckphone'=>$bizhong['phone']);
		echo json_encode($returndata);
		return;
	}


	$flag_m=new M('flag');
	$data=$flag_m->find('flag=2 and openid not in(select openid from  weixin_xingyunshoujihao where  (status=2 or designated=2) or (ordernum='.$currentordernum.' and designated=3)) order by rand() limit 1','openid,phone');
	
	if($data){
		$zhongjiangdata=array('openid'=>$data['openid'],'ordernum'=>$currentordernum,'status'=>2,'created_at'=>time(),'designated'=>1);
		$xingyunshoujihao_m->add($zhongjiangdata);
		$returndata=array('code'=>1,'luckphone'=>$data['phone']);
		echo json_encode($returndata);
		return;
	}
	

}



