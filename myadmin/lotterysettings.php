<?php
require_once('Page.php');
class LotterySettings extends Page{
	function show(){
		$system_config_m = new M('system_config');
		//抽奖
		$data=$system_config_m->find('configkey="cjshowtype"');
		$cjshowtype=intval($data['configvalue']);
		//3d抽奖
		$data=$system_config_m->find('configkey="threedimensionallotteryshowtype"');
		$threedimensionallotteryshowtype=intval($data['configvalue']);
		//抽奖箱
		$data=$system_config_m->find('configkey="cjxshowtype"');
		$cjxshowtype=intval($data['configvalue']);
		//砸金蛋
		$data=$system_config_m->find('configkey="zjdshowtype"');
		$zjdshowtype=intval($data['configvalue']);
		//抽奖显示方式
		$this->assign('cjshowtype',$cjshowtype);
		//3d抽奖显示方式
		$this->assign('threedimensionallotteryshowtype',$threedimensionallotteryshowtype);
		//抽奖箱显示方式
		$this->assign('cjxshowtype',$cjxshowtype);
		//砸金蛋显示方式
		$this->assign('zjdshowtype',$zjdshowtype);
		
		$this->display('templates/lotterysettings.html');
	}
}
$page=new LotterySettings();
$page->setTitle('抽奖设置');
$page->show();
