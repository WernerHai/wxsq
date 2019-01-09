<?php
require_once('Page.php');
class Intergrate extends Page{
	function show(){
		$weixin_config_m=new M('weixin_config');
		$weixin_config=$weixin_config_m->find('1');
		$this->assign('weixin_config',$weixin_config);
		$this->assign('domain',$_SERVER['HTTP_HOST']);
		$this->display('templates/intergrate.html');
	}
}
$page=new Intergrate();
$page->setTitle('对接设置');
$page->show();
