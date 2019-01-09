<?php
require_once('Page.php');
class SystemSettings extends Page{
	function show(){
		$weixin_config_m=new M('weixin_config');
		$weixin_config=$weixin_config_m->find('1');
		$erweima=GetAttachmentById($weixin_config['erweima']);
		$weixin_config['erweima']=$erweima['filepath'];
		$this->assign('weixin_config',$weixin_config);
		$this->display('templates/systemsettings.html');
	}
}
$page=new SystemSettings();
$page->setTitle('系统设置');
$page->show();
