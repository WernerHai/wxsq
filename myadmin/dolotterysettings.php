<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once(dirname(__FILE__) . '/../common/CacheFactory.php');
require_once('../common/session_helper.php');
$action=$_GET['action'];
switch ($action){
	case 'set_showstyle':
		set_showstyle();
		break;
}

function set_showstyle(){
	$key=isset($_GET['key'])?$_GET['key']:'';
	$val=isset($_GET['val'])?intval($_GET['val']):1;
	if(!in_array($key,array('cjshowtype','threedimensionallotteryshowtype','cjxshowtype','zjdshowtype'))){
		echo '{"code":-1,"message":"数据格式错误"}';
		return ;
	}
	$val=($val>3 ||$val<1)?1:$val;
	$system_config_m = new M('system_config');
	$return=$system_config_m->update('configkey="'.$key.'"', array('configvalue'=>$val));
	if($return){
		echo '{"code":1,"message":"修改成功"}';
		return ;
	}else{
		echo '{"code":-2,"message":"修改失败"}';
		return ;
	}
}