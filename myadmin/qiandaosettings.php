<?php
require_once('Page.php');
class QiandaoSettings extends Page{
	function show(){
		$system_config_m = new M('system_config');
		$data=$system_config_m->find('configkey="signnameshowstyle"');
		$signnameshowstyle=intval($data['configvalue']);
		//签到人名显示方式
		$this->assign('signnameshowstyle',$signnameshowstyle);
		$this->display('templates/qiandaosettings.html');
	}
}
$page=new QiandaoSettings();
$page->setTitle('签到设置');
$page->show();
