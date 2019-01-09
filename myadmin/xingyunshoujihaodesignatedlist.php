<?php
//幸运手机号内定列表
require_once('Page.php');
class Xingyunshoujihaodesignatedlist extends Page{
	function show(){
// 		$flag_m=new M('flag');
// 		$flag=$flag_m->select('phone !="" and !ISNULL(phone) and (designated=2 or designated=3) order by ordernum asc',
// 				'weixin_xingyunshoujihao.designated,weixin_xingyunshoujihao.status,weixin_xingyunshoujihao.ordernum,weixin_flag.phone,weixin_flag.avatar,weixin_flag.nickname',
// 				'','assoc','left join weixin_xingyunshoujihao on weixin_xingyunshoujihao.openid=weixin_flag.openid');
		$xingyunshoujihao_m=new M('xingyunshoujihao');
		$xingyunshoujihao=$xingyunshoujihao_m->select('(designated=2 or designated=3) order by ordernum asc',
				'weixin_xingyunshoujihao.id,weixin_xingyunshoujihao.designated,weixin_xingyunshoujihao.status,weixin_xingyunshoujihao.ordernum,weixin_flag.phone,weixin_flag.avatar,weixin_flag.nickname',
				'','assoc','left join weixin_flag on weixin_xingyunshoujihao.openid=weixin_flag.openid');
		

// 		echo var_export($xingyunshoujihao);
		$statustext=array('未中','未中','已中奖');
		$designatedtext=array('未设置','未设置','必中','不会中');
		foreach($xingyunshoujihao as $k=>$v){
			$xingyunshoujihao[$k]['designatedtext']=$designatedtext[intval($v['designated'])];
			$xingyunshoujihao[$k]['statustext']=$statustext[intval($v['status'])];
			$xingyunshoujihao[$k]['nickname']=pack('H*',$v['nickname']);
		}

		$this->assign('xingyunshoujihao',$xingyunshoujihao);
		$this->display('templates/xingyunshoujihaodesignatedlist.html');
	}
}
$page=new Xingyunshoujihaodesignatedlist();
$page->setTitle('幸运手机号内定列表');
$page->show();
