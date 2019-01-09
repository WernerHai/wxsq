<?php
require_once('Page.php');
class ShakeResult extends Page{
	function show(){
		$currentroundno=isset($_GET['roundno'])?intval($_GET['roundno']):0;
		$page=isset($_GET['page'])?intval($_GET['page']):1;
		$pagesize=20;
		//SELECT  `weixin_shake_toshake`.*,`weixin_flag`.`phone` ,`weixin_flag`.`signname`  FROM `com_wxmiao_xianchang`.`weixin_shake_toshake` left join `weixin_flag` on `weixin_flag`.`openid` = `weixin_shake_toshake`.openid  where roundno=0 order by point desc limit 0,20
		$where=' roundno='.$currentroundno.' order by point desc limit '.(($page-1)*$pagesize).','.$pagesize;
// 		echo $where;exit();
		$countwhere=' roundno='.$currentroundno;
		
		$shake_config_m=new M('shake_config');
		$shake_config=$shake_config_m->find('1');
		$this->assign('shake_config',$shake_config);
		
		$shake_toshake_m=new M('shake_toshake');
// 		$shake_toshake=$shake_toshake_m->select($where);
		$shake_toshake=$shake_toshake_m->select($where,'`weixin_shake_toshake`.*,`weixin_flag`.`phone` ,`weixin_flag`.`signname`','','','left join `weixin_flag` on `weixin_flag`.`openid` = `weixin_shake_toshake`.openid');
// 		echo var_export($shake_toshake);exit();
		$shake_toshake_count=$shake_toshake_m->find($countwhere,'*','count');
		$shake_toshake_count=empty($shake_toshake_count)?0:$shake_toshake_count;
		//echo var_export($shake_toshake_count);
		$url='shakeresult.php?roundno='.$currentroundno;
		$this->assign('pagerhtml',$this->pagerhtml($page, $pagesize, $shake_toshake_count, $url));
		$shake_toshake=$this->processshake_toshake($shake_toshake, $page,$pagesize);
		//echo var_export($shake_toshake);exit();
		$this->assign('shake_toshake',$shake_toshake);
		$this->assign('currentroundno',$currentroundno);
		$this->display('templates/shakeresult.html');
	}
	function processshake_toshake($shake_toshake,$page,$pagesize){
		//echo var_export($shake_toshake);
		$newshake_toshake=array();
		$i=1;
		foreach($shake_toshake as $item){
			$newitem['rank']=$i+($page-1)*$pagesize;
			$newitem['openid']=$item['openid'];
			$newitem['nickname']=pack('H*', $item['nickname']);
			$newitem['avatar']=$item['avatar'];
			$newitem['phone']=$item['phone'];
			$newitem['signname']=$item['signname'];
			$newshake_toshake[]=$newitem;
			$i++;
		}
		return $newshake_toshake;
	}
}
$page=new ShakeResult();
$page->setTitle('摇一摇结果');
$page->show();
