<?php
require_once('Page.php');
require_once("../wall/biaoqing.php");
class Zjlist extends Page{
	function show(){
		$currentplug=isset($_GET['plug'])?strval($_GET['plug']):'cj';
		$page=isset($_GET['page'])?intval($_GET['page']):1;
		$pagesize=20;
		$where='status >= 2 and fromplug="'.$currentplug.'" order by awardid limit '.(($page-1)*$pagesize).','.$pagesize;
		$countwhere='status >= 2 and fromplug="'.$currentplug.'"';
		$view_zjlist_m=new M('view_zjlist');
		$zjlist=$view_zjlist_m->select($where);
		$countzjlist=$view_zjlist_m->find($countwhere,'*','count');
		$countzjlist=empty($countzjlist)?0:$countzjlist;
		$baseurl='zjlist.php?plug='.$currentplug;
		foreach($zjlist as $key=>$val){
			$val['nickname']=pack('H*', $val['nickname']);
			$val=emoji_unified_to_html(emoji_softbank_to_unified($val));
			//$val['fjstatus']=empty($val['fjdatetime'])?'未发':'已发';
			$zjlist[$key]=$val;
		}
		$plugs_m=new M('plugs');
		$cjplugs=$plugs_m->select('choujiang>0 order by ordernum desc');
		
		$this->assign('currentplug',$currentplug);
		$this->assign('cjplugs', $cjplugs);
		$this->assign('zjlist',$zjlist);
		$this->assign('pagerhtml',$this->pagerhtml($page, $pagesize, $countzjlist, $baseurl));
		
		$this->display('templates/zjlist.html');
	}
}
$page=new Zjlist();
$page->setTitle('中奖列表');
$page->show();
