<?php
require_once('Page.php');
class VoteItems extends Page{
	function show(){
		$vote_m=new M('vote');
		$voteitems=$vote_m->select('1 order by id desc');
		$this->assign('votelistjson',json_encode($voteitems));
		$this->display('templates/voteitems.html');
	}
}
$page=new VoteItems();
$page->setTitle('投票选项管理');
$page->show();
