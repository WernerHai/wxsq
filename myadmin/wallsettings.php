<?php
require_once('Page.php');
class WallSettings extends Page{
	function show(){
		$system_config_m = new M('system_config');
		$data=$system_config_m->find('configkey="wallnameshowstyle"');
		$wallnameshowstyle=intval($data['configvalue']);

		//上墙名称显示方式
		$this->assign('wallnameshowstyle',$wallnameshowstyle);
		$this->display('templates/wallsettings.html');
	}
}
$blank=new WallSettings();
$blank->setTitle('上墙设置');
$blank->show();
