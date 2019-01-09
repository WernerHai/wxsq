<?php
require_once('Page.php');
class Redpacketconfig extends Page{
	function show(){
		$redpacket_config_m=new M('redpacket_config');
		$redpacket_config=$redpacket_config_m->find('1');

		$this->assign('redpacket_config', $redpacket_config);
		$this->display('templates/redpacketconfig.html');
	}
}
$page=new Redpacketconfig();
$page->setTitle('红包配置页面');
$page->show();
