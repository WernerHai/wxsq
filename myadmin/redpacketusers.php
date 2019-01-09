<?php
require_once('Page.php');
class Redpacketusers extends Page{
	function show(){
		$redpacket_users_model=new M('redpacket_users');
		$redpacket_users=$redpacket_users_model->select('weixin_redpacket_users.userid>0 order by roundid asc','weixin_redpacket_users.*,weixin_flag.nickname,weixin_flag.avatar','','assoc','left join weixin_flag on weixin_flag.id=weixin_redpacket_users.userid');
		$redpacket_users=$this->processuserlist($redpacket_users);
		$this->assign('redpacketusers',$redpacket_users);
		$this->display('templates/redpacketusers.html');
	}
	function processuserlist($redpacket_users){
		$newredpacket_users=array();
		foreach($redpacket_users as $k=>$v){
			$row=array(
					'id'=>$v['id'],
					'userid'=>$v['userid'],
					'openid'=>$v['openid'],
					'roundid'=>$v['roundid'],
					'amount'=>($v['amount']/100).'元',
					'nickname'=>pack('H*', $v['nickname']),
					'avatar'=>$v['avatar'],
					'updated_at'=>date('Y-m-d H:i:s',$v['updated_at'])
			);
			$newredpacket_users[]=$row;
		}
		return $newredpacket_users;
	}
}
$page=new Redpacketusers();
$page->setTitle('红包用户列表');
$page->show();