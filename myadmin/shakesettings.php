<?php
require_once('Page.php');
class ShakeSettings extends Page{
	function show(){
		$shake_config_m=new M('shake_config');
		$shake_config=$shake_config_m->find('1');
		$this->assign('shake_config', $shake_config);
		$this->display('templates/shakesettings.html');
	}
}
$page=new ShakeSettings();
$page->setTitle('摇一摇设置');
$page->show();
