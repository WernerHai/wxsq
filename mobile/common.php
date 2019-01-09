<?php
// define('APPPATH', );
define('COMPILEPATH',str_replace(DIRECTORY_SEPARATOR.'mobile', '', dirname(__FILE__)).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR);
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once(dirname(__FILE__) . '/../common/http_helper.php');
require_once(dirname(__FILE__) . '/../common/weixin_helper.php');
require_once(dirname(__FILE__) . '/../common/url_helper.php');
require_once(dirname(__FILE__) . '/../common/Attachment_helper.php');
require_once(dirname(__FILE__) . '/../common/function.php');

$currenturl=request_scheme().'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].($_SERVER['QUERY_STRING']==''?'':'?'.$_SERVER['QUERY_STRING']);
$wall_config= getWallConf();
$weixin_config=getWeixinConf();
// $wall_config['rentweixin']1借用其他微信服务号获取用户信息2表示使用微赢的现场活动公众号授权，默认值为2，选2可以不要对接任何东西直接使用
if($wall_config['rentweixin']==1){//使用用户自己的公众号
	if(!isset($_GET['rentopenid'])){//如果还没有获取到openid
		if(empty($_GET['vcode']) || $_GET['vcode']!=$wall_config['verifycode']){
			echo '找不到活动';
			exit();
		}
		if (empty($_GET['code'])) {//还没有获取到code
			$fromurl=$currenturl;
			$url=getauthorizeurl($fromurl, 'snsapi_userinfo', $weixin_config['appid']);
			header('location:' . $url);
			exit();
		}else{//获取到code之后获取用户信息
			$tokeninfo = getaccess_token($_GET['code'], $weixin_config['appid'], $weixin_config['appsecret']);
			$tokeninfo = json_decode($tokeninfo, true);
			$userinfo = getsnsuserinfo($tokeninfo['access_token'], $tokeninfo['openid']);
			$userinfo = json_decode($userinfo);
			if(is_string($userinfo)){
				$userinfo = json_decode($userinfo,true);
			}
			$userinfo['nickname']=bin2hex($userinfo['nickname']);
			$userinfo['openid']=$tokeninfo['openid'];
			$userinfo['rentopenid']=$tokeninfo['openid'];
			writeremoteuserinfo($userinfo);

			$url_arr=parse_url($currenturl);
			$baseurl=$url_arr['scheme'].'://'.$url_arr['host'].$url_arr['path'];

// 			if(strpos($currenturl,'qiandao.php')===false){//刚获取到用户信息还没有签到
				header('location:'.$baseurl.'?rentopenid='.$userinfo['openid']);
				exit();
// 			}
		}
	}else{//获取到用户信息之后
		$openid=$_GET['rentopenid'];
		$flag_m=new M('flag');
		$userinfo=$flag_m->find('openid="'.$openid.'"');		
		if($userinfo['flag']==1){//如果检查用户还没有签到
			if(strpos($currenturl,'qiandao.php')===false){
				header('location:/mobile/qiandao.php?rentopenid='.$userinfo['rentopenid'].'&fromurl='.urlencode($currenturl));
				exit();
			}
		}else{
// 			echo $currenturl;exit();
// 			echo 'location:'.urldecode($_GET['fromurl']).'?rentopenid='.$openid;exit();
// 			header('location:'.urldecode($_GET['fromurl']).'?rentopenid='.$openid);
// 			exit();
		}
	}
}else{
	if(!isset($_GET['rentopenid'])){
		if(empty($_GET['vcode']) || $_GET['vcode']!=$wall_config['verifycode']){
			echo '找不到活动';
			exit();
		}
		//先去获取用户信息
		$url='http://api.vdcom.cn/wxgate/index?url='.urlencode($currenturl);
		header('location:'.$url);
		exit();
	}else{
		$openid=$_GET['rentopenid'];
		$flag_m=new M('flag');
		$userinfo=$flag_m->find('openid="'.$openid.'"');
		if(!$userinfo){
			$url='http://api.vdcom.cn/wxgate/getuserinfobyrentopenid?rentopenid='.$_GET['rentopenid'];
			require_once('../common/http_helper.php');
			$json=http_get($url);
			$userinfo_arr=json_decode($json, true);
			if($userinfo_arr['error']>0){
				$userinfo=array();
				$userinfo['openid']=$userinfo_arr['userinfo']['openid'];
				$userinfo['rentopenid']=$userinfo_arr['userinfo']['openid'];
				$userinfo['nickname']=$userinfo_arr['userinfo']['nickname'];
				$userinfo['headimgurl']=$userinfo_arr['userinfo']['headimgurl'];
				$userinfo['sex']=$userinfo_arr['userinfo']['sex'];
				writeremoteuserinfo($userinfo);
			}
			
			if(strpos($currenturl,'qiandao.php')===false){
				header('location:/mobile/qiandao.php?rentopenid='.$userinfo['rentopenid'].'&fromurl='.urlencode($currenturl));
				exit();
			}
			
		}else{
			if($userinfo['flag']==1){
				if(strpos($currenturl,'qiandao.php')===false){
					header('location:/mobile/qiandao.php?rentopenid='.$userinfo['rentopenid'].'&fromurl='.urlencode($currenturl));
					exit();
				}
			}
		}
		
	}
}



function writeremoteuserinfo($info){
	//解决转义符
	//json字符串转化成数组
	$infoarr = json_encode($info);
	$infoarr = json_decode($infoarr, true);
	$flag = new M('flag');
	$count = $flag->find("openid='" . $infoarr['openid'] . "'", '*', 'count');
	$flag_info=$flag->find("openid='" . $infoarr['openid'] . "'");
	if(intval($flag_info['flag'])>=2){
		return 'ok';
	}
	$sqlarr = array(
			"openid"=>$infoarr['openid'],
			"rentopenid"=>$infoarr['rentopenid'],
			"nickname" => $infoarr['nickname'],
			"avatar" => $infoarr['headimgurl'],
			"fakeid" => randStr(),
			"sex" => $infoarr['sex'],
			"fromtype" => 'weixin',
			"datetime" => time(),
			"flag" => "1"
	);
	
	if (isset($infoarr['shadyphone'])) {
		$shady = new M('cj_shady');
		$shadyarr = $shady->find("phone=" . $infoarr['shadyphone']);
		if (empty($shadyarr)) {
			$addarr = array(
					'phone' => $infoarr['shadyphone'],
					'shady' => $shadyarr['grade']
			);
			$sqlarr = array_merge($sqlarr, $addarr);
		}
	}
	if ($count) {
		$savve = $flag->update("openid='" . $infoarr['openid'] . "'", $sqlarr);
	}else{
		$savve = $flag->add($sqlarr);
	}
	if ($savve) {
		return "ok";
	}
}


function randStr($len = 10)
{
	$rand='';
	for ($i = 0; $i < $len; $i++) {
		$rand .= mt_rand(0, 9);
	}
	return $rand;
}