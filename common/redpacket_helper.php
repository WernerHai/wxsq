<?php

require_once 'wxpay/lib/WxPay.Api.php';
//发送普通红包
function sendredpacket($openid,$amount,$sendname='',$wishing='恭喜发财'){
	$redpacketdata=new WxPayRedPack();
	$orderno=genMch_billno();
	$redpacketdata->SetOrderno($orderno);
	$redpacketdata->SetAct_name('红包雨');
	$redpacketdata->SetRe_openid($openid);
	$redpacketdata->SetRemark('红包雨');
	$redpacketdata->SetSend_name($sendname);
	$redpacketdata->SetTotal_amount($amount);
	$redpacketdata->SetTotal_num(1);
	$redpacketdata->SetWishing($wishing);
	$result=WxPayApi::sendredpack($redpacketdata,6);
	return $result;
}

function genMch_billno(){
	$weixin_config_m=new M('weixin_config');
	$weixin_config=$weixin_config_m->find('1');
	$mch_billno=$weixin_config['mch_id'].date('YmdHis',time()).rand(1000000000,9999999999);
	return $mch_billno;
}