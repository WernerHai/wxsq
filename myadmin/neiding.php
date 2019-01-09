<?php
require_once('Page.php');
class Neiding extends Page{
	function show(){
		//$currentplug=isset($_GET['plug'])?strval($_GET['plug']):'cj';
		$page=isset($_GET['page'])?intval($_GET['page']):1;
		$pagesize=20;
		$where='designated >1  order by awardid limit '.(($page-1)*$pagesize).','.$pagesize;
		$countwhere='designated >1 ';
		$view_zjlist_m=new M('view_zjlist');
		$zjlist=$view_zjlist_m->select($where);
		$countzjlist=$view_zjlist_m->find($countwhere,'*','count');
		$countzjlist=empty($countzjlist)?0:$countzjlist;
		$baseurl='neiding.php';
		require_once 'biaoqing.php';
		foreach($zjlist as $key=>$val){
			$val['nickname']=pack('H*', $val['nickname']);
			$val=emoji_unified_to_html(emoji_softbank_to_unified($val));
			$zjlist[$key]=$val;
		}
		$plugs_m=new M('plugs');
		$cjplugs=$plugs_m->select('choujiang>0 order by ordernum desc');
		$plugs=array();
		foreach($cjplugs as $item){
			$plugs[$item['name']]=$item['title'];
		}
		foreach($zjlist as $k=>$v){
			$zjlist[$k]['cjtype']=$plugs[$v['fromplug']];
		}
		
		$this->assign('currentplug',$currentplug);
		$this->assign('zjlist',$zjlist);
		$this->assign('pagerhtml',$this->pagerhtml($page, $pagesize, $countzjlist, $url));
		$this->display('templates/neiding.html');
	}
}
$page=new Neiding();
$page->setTitle('内定列表');
$page->show();
