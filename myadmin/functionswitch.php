<?php
require_once('Page.php');
class Blank extends Page{
	function show(){
		$plugs_m=new M('plugs');
		$plugs=$plugs_m->select('1 order by id asc');
		$this->assign('plugs',$plugs);
		$this->display('templates/functionswitch.html');
	}
}
$page=new Blank();
$page->setTitle('功能开关');
$page->show();
