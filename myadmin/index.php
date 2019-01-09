<?php
require_once('Page.php');
class Index extends Page{
	function show(){

		$weixin_config=getWeixinConf();

		$this->assign('weixin_config',$weixin_config);
		$this->display('templates/index.html');
	}
}
$page=new Index();
$page->setTitle('首页');
$page->show();
