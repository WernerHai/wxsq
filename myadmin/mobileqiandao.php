<?php
require_once('Page.php');
class MobileQiandao extends Page{
	function show(){
		$system_config_m = new M('system_config');
		$data=$system_config_m->find('configkey="mobileqiandaobg"');
		$mobileqiandaobg=GetAttachmentById(intval($data['configvalue']));
// 		echo var_export($mobileqiandaobg);
		$this->assign('mobileqiandaobg',$mobileqiandaobg['filepath']);
		$this->display('templates/mobileqiandao.html');
	}
}
$page=new MobileQiandao();
$page->setTitle('手机签到页面设置');
$page->show();
