<?php
require_once('Page.php');
class Qiandao extends Page{
	function show(){
		$page=isset($_GET['page'])?intval($_GET['page']):1;
		$search=isset($_GET['search'])?strval($_GET['search']):'';
		$page=$page<1?1:$page;
		$pagesize=20;
		$flag_m=new M('flag');
		$searchwhere='';
		if(!empty($search)){
			$searchwhere='(nickname like "%'.bin2hex($search).'%" or signname like "%'.$search.'%" or phone like "%'.$search.'%") and ';
		}
		$where = $searchwhere.'1 and signorder>0 order by id desc limit '.(($page-1)*$pagesize).','.$pagesize;
		$flagcountwhere=$searchwhere.'1 and signorder>0 order by id desc';
		$flaglist=$flag_m->select($where);
		$flagcount=$flag_m->find($flagcountwhere,'*','count');
		$baseurl='qiandao.php'.(empty($search)?'':'?search='.$search);
		
		$award_m=new M("award");
		$awardlist=$award_m->select(' isdel=1 ');
		$plugs_m=new M('plugs');
		$cjplugs=$plugs_m->select('choujiang>0 order by ordernum desc');
		$this->assign('cjplugs', $cjplugs);
		$this->assign('pagehtml',$this->pagerhtml($page,$pagesize,$flagcount,$baseurl));
		$this->assign('searchtext',$search);
		$this->assign('awardlist',$awardlist);
		$this->assign('flaglist',$this->processflag($flaglist));
		$this->display('templates/qiandao.html');
	}
	function processflag($flaglist){
		$newflaglist=array();
		foreach($flaglist as $item){
			$newitem['openid']=$item['openid'];
			$newitem['nickname']=pack('H*', $item['nickname']);
			$newitem['avatar']=$item['avatar'];
			$newitem['phone']=$item['phone'];
			$newitem['signname']=$item['signname'];
			$newitem['status']=$item['status'];
			$newflaglist[]=$newitem;
		}
		return $newflaglist;
	}
}

$page=new Qiandao();
$page->setTitle('签到管理');
$page->show();
