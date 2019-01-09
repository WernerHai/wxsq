<?php
require_once('Page.php');
class Blank extends Page{
	function show(){
// 		$wall_config_m=new M('wall_config');
// 		$wall_config=$wal
		$this->display('templates/votesettings.html');
	}
}
$page=new Blank();
$page->setTitle('投票设置');
$page->show();
