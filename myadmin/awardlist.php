<?php
require_once('Page.php');
class Blank extends Page{
	function show(){
		$award_m=new M('award');
		$awardlist=$award_m->select('isdel = 1');
		foreach($awardlist as $key=>$v){
			if($v['imagepath']>0){
				$img=GetAttachmentById($v['imagepath']);
				$awardlist[$key]['imagepath']=$img['filepath'];
			}else{
				$awardlist[$key]['imagepath']='/wall/themes/meepo/assets/images/defaultaward.jpg';
			}
			
			
		}
		$this->assign('awardlist',$awardlist);
		$this->display('templates/awardlist.html');
	}
}
$page=new Blank();
$page->setTitle('奖品管理页');
$page->show();
