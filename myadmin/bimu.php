<?php
require_once('Page.php');
class Blank extends Page{
	function show(){
		$bimu_config_m=new M('bimu_config');
		$bimu_config=$bimu_config_m->find('1');
		$bimuimage='';
		if(isset($bimu_config) && !empty($bimu_config['imagepath'])){
			$img=GetAttachmentById($bimu_config['imagepath']) ;
			$bimuimage=$img['filepath'];
		}
		$bimu_config['image']=$bimuimage;
		$this->assign('bimu_config',$bimu_config);
		//$this->assign('bimuimage',$bimuimage);
		$this->display('templates/bimu.html');
	}
}
$page=new Blank();
$page->setTitle('闭幕墙');
$page->show();
