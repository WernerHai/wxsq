<?php
error_reporting ( E_ALL );
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once(dirname(__FILE__) . '/../common/Attachment_helper.php');
require_once(dirname(__FILE__) . '/../common/function.php');

$action = $_GET ['action'];
switch ($action) {
	case 'user_register' :
		user_register ();
		break;
	case 'msg_getmore' :
		msg_getmore ();
		break;
	case 'msg_send' :
		send_msg ();
		break;
	case 'msg_uploadimg' :
		msg_uploadimg ();
		break;
	case 'shake_count':
		shake_count();
		break;
	case 'vote_insert':
		vote_insert();
		break;
}
function msg_getmore() {
	include ('../wall/biaoqing.php');
	$wall_m = new M ( 'wall' );
	$openid = $_POST ['openid'];
	
	$flag_m = new M ( 'flag' );
	$myinfo = $flag_m->find ( 'openid="' . $openid . '"' );
	if($myinfo['status']==2){
		$data = array (
				'message' => '您已经被屏蔽，无法继续参与活动',
				'errno' => -3
		);
		echo trim ( json_encode ( $data ) );
		return;
	}
	
	$messagelist = $wall_m->select ( 'openid="' . $openid . '" order by datetime desc' );
	foreach ( $messagelist as $k => $message ) {
		$message ['nickname'] = pack ( 'H*', $message ['nickname'] );
		$message ['content'] = pack ( 'H*', $message ['content'] );
		$message = emoji_unified_to_html ( emoji_softbank_to_unified ( $message ) );
		$message ['content'] = biaoqing ( $message ['content'] );
		
		$message ['type'] = 1;
		if (! empty ( $message ['image'] )) {
			$message ['type'] = 2;
			$message ['content'] = $message ['image'];
		}
		$message ['createtime'] = $message ['datetime'];
		$messagelist [$k] = $message;
	}
	// echo var_export($messagelist);
	$msg = array (
			'message' => $messagelist,
			'errno' => 0 
	);
	echo trim ( json_encode ( $msg ) );
	// echo var_export($messages);
}

// todo:自动审核关键字处理
// todo:手动审核的默认审核状态
function send_msg() {
	$openid = $_POST ['openid'];
	$content = htmlentities($_POST ['content'],ENT_NOQUOTES,"utf-8");
	$flag_m = new M ( 'flag' );
	$myinfo = $flag_m->find ( 'openid="' . $openid . '"' );
	if($myinfo['status']==2){
		$data = array (
				'message' => '您已经被屏蔽，无法继续参与活动',
				'errno' => -3
		);
		echo trim ( json_encode ( $data ) );
		return;
	}
	
	$wall_m = new M ( 'wall' );
	$mylastmsg=$wall_m->find('openid="'.$openid.'" order by datetime desc limit 1');

	$wall_config = getWallConf();
	if(!empty($mylastmsg) && time()-$mylastmsg['datetime']<$wall_config['timeinterval']){
		$data = array (
				'message' => '你发送消息的速度太快了',
				'errno' => -2
		);
		echo trim ( json_encode ( $data ) );
		return false;
	}
	

	require_once ('../common/emo_helper.php');
	
	$content = Emo::ProcessEmoMsg ( $content );
	$ret = 1;
	if ($wall_config ['shenghe'] == 1) {
		$ret = 0;
	} else {
		if (blackword ( $content, $wall_config ) == 1) {
			$ret = 0;
		}
	}
	$maxshenheorder=0;
	if($ret==1){
		$maxshenheorder=time();
	}
	
	$message = array (
			'messageid' => 0,
			'fakeid' => $myinfo ['fakeid'],
			'num' => 0,
			'content' => $content,
			'nickname' => $myinfo ['nickname'],
			'avatar' => $myinfo ['avatar'],
			'ret' => $ret,
			'fromtype' => 'weixin',
			'image' => 0,
			'datetime' => time (),
			'openid' => $openid ,
			'shenhetime'=>$maxshenheorder,
	);
	$messageadd = $message;
	$messageadd ['content'] = bin2hex ( $messageadd ['content'] );
	$wall_m->add ( $messageadd );
	$message ['tip'] =$ret==1?'发送成功':'发送成功，请等待管理员审核';
	$data = array (
			'message' => $message,
			'errno' => 0 
	);
	echo trim ( json_encode ( $data ) );
}
function msg_uploadimg() {
	$openid = $_POST ['msg_send'];
	
	$flag_m = new M ( 'flag' );
	$myinfo = $flag_m->find ( 'openid="' . $openid . '"' );
	if($myinfo['status']==2){
		$data = array (
				'message' => '您已经被屏蔽，无法继续参与活动',
				'errno' => -3
		);
		echo trim ( json_encode ( $data ) );
		return;
	}
	$wall_m = new M ( 'wall' );
	$mylastmsg=$wall_m->find('1 order by datetime desc limit 1');
// 	$wall_config_m = new M ( 'wall_config' );
	$wall_config =getWallConf();// $wall_config_m->find ( '1' );
	
	if(!empty($mylastmsg) && time()-$mylastmsg['datetime']<$wall_config['timeinterval']){
		$data = array (
				'message' => '你发送消息的速度太快了',
				'errno' => -2
		);
		echo trim ( json_encode ( $data ) );
		return false;
	}
	
	$base64=$_POST['imgbase64'];
	$extension=$_POST['filetype'];


	require_once ('../common/http_helper.php');
	require_once ('../common/weixin_helper.php');
	require_once ('../common/FileUploadFactory.php');
	
	$fuf = new FileUploadFactory (SAVEFILEMODE);
	$base64= str_replace('data:image/jpg;base64,', '', $base64);
	$base64= str_replace('data:image/jpeg;base64,', '', $base64);
	$base64= str_replace('data:image/png;base64,', '', $base64);
	$file = base64_decode($base64);

	$attachement_data=$fuf->SaveFile( $file, $extension, $wall_config['web_root']);

	$ret = $wall_config ['shenghe'] == 1 ? 0 : 1;
	$returndata = array (
			'errno' => 0,
			'message' => array (
					"picurl" => $attachement_data['filepath'],
					"tip" =>($ret==1?'发送成功':'发送成功，请等待管理员审核') 
			) 
	);
	$maxshenheorder=0;
	if($ret==1){
		$maxshenheorder=time();
	}
	// 记录信息到数据库
	$data = array(
			'messageid' => 0,
			'fakeid' => $myinfo['fakeid'],
			'num' => 0,
			'content' =>'此消息为图片！',
			'nickname' =>$myinfo['nickname'],
			'avatar' =>$myinfo['avatar'],
			'ret' => $ret,
			'fromtype' => 'weixin',
			'image' => $attachement_data['id'],
			'datetime' => time(),
			'openid' => $openid ,
			'shenhetime'=>$maxshenheorder
	);
	
	$return = $wall_m->add ( $data );
	if ($return == false) {
		$returndata = array (
				'errno' => - 1,
				'message' => array (
						"picurl" => $attachement_data['filepath'],
						"tip" => "信息保存失败！" 
				) 
		);
	}
	echo json_encode ( $returndata );
}

function user_register() {
	$openid = $_POST ['openid'];
	$redirect=isset($_POST['redirecturl'])?$_POST['redirecturl']:'';
// 	$wall_config_m=new M('wall_config');
	$wall_config=getWallConf();//$wall_config_m->find('1');
	$signname='';
	$phone = '';
	$flag_m=new M('flag');
	$usercount=$flag_m->find('flag=2 and openid="'.$openid.'"','*','count');
	if($usercount>0){
		$str=empty($redirect)?'':',"redirecturl":"'.$redirect.'"';
		echo '{"errno":0,"message":"签到成功"'.$str.'}';
		return;
	}
	
	$flag_count=$flag_m->find('flag=2','*','count');
	$maxplayers=intval($wall_config['maxplayers']);
	if($maxplayers>0){
		if($flag_count>=$maxplayers){
			echo '{"errno":-5,"message":"活动人数已经满了"}';
			return ;
		}
	}
	
	if($wall_config['name_switch']==1){
		$signname = isset ( $_POST ['realname'] ) ? $_POST ['realname'] : '';
		$signname =htmlentities($signname,ENT_NOQUOTES,"utf-8");
		if($signname==''){
			echo '{"errno":-2,"message":"姓名必须填写"}';
			return ;
		}
	}
	if($wall_config['phone_switch']==1){
		$phone = isset ( $_POST ['mobile'] ) ? $_POST ['mobile'] : '';
		
		if($phone==''){
			echo '{"errno":-3,"message":"手机号必须填写"}';
			return ;
		}
		if (! preg_match ( "/^1[0-9]{1}\d{9}$/", $phone )) {
			$phone = '';
		}
		if($phone==''){
			echo '{"errno":-4,"message":"手机号格式不正确"}';
			return ;
		}
	}
	
	//signorder签到顺序也记录一下
	$signorder=$flag_m->find('1','signorder','max');
	$signorder=isset($signorder)?intval($signorder):0;

	$sql_check = "UPDATE  `weixin_flag` SET `flag` = '2',`phone` = '{$phone}',`signname` = '{$signname}',`status`=1,`signorder`='".($signorder+1)."' WHERE  `openid` =  '{$openid}';";
	$note_sql = $flag_m->query ( $sql_check );
	if ($note_sql) {
		$str=empty($redirecturl)?'':',"redirecturl":"'.$redirecturl.'"';
		echo '{"errno":0,"message":"签到成功"'.$str.'}';
	} else {
		echo '{"errno":-1,"message":"签到信息记录失败"}';
	}
}
function shake_count(){
	require_once(dirname(__FILE__) . '/../common/CacheFactory.php');
	$openid = $_POST['openid'];
	$flag_m = new M ( 'flag' );
	$myinfo = $flag_m->find ( 'openid="' . $openid . '"' );
	if($myinfo['status']==2){
		$data = array (
				'message' => '您已经被屏蔽，无法继续参与活动',
				'status' => -3
		);
		echo trim ( json_encode ( $data ) );
		return;
	}
	hasmysql($openid);
	// return;
	//实例化一个memcache对象
// 	$datapath = str_replace("/mobile", '/', dirname(__FILE__));
// 	$configpath = $datapath . '/data/memcache.php';
	
// 	if (file_exists($configpath)) {
// 		require($configpath);
// 		if ($use_memcache == 1) {
// 			$mem = new CacheFactory('memcached');  //声明一个新的memcached链接
// 			hasmemcache($mem,$openid);
// 		}else{
// 			hasmysql($openid);
// 		}
// 	}else{
// 		hasmysql($openid);
// 	}
	
	
	
}

function vote_insert(){
	$openid = $_POST ['openid'];
	$flag_m = new M ( 'flag' );
	$myinfo = $flag_m->find ( 'openid="' . $openid . '"' );
	if($myinfo['status']==2){
		$data = array (
				'message' => '您已经被屏蔽，无法继续参与活动',
				'errno' => -3
		);
		echo trim ( json_encode ( $data ) );
		return;
	}
	
	$voteitems_arr= isset ( $_POST ['data'] ) ? $_POST ['data'] : '';
	$voteitems=implode(',',$voteitems_arr);
// 	$wall_config_m=new M('wall_config');
	$wall_config=getWallConf();//$wall_config_m->find(1);
	if(count($voteitems_arr)>$wall_config['votecannum']){
		echo '{"errno":-1,"message":"你没有选择项目或者选择的项目太多了！"}';
		return ;
	}else{
		//$flag_m=new M('flag');
// 		$flag=$flag_m->find("`openid` = '{$openid}'");
		if(!empty($myinfo['vote'])){
			echo '{"errno":-2,"message":"您已经投过票了！"}';
			return;
		}
		$flag_m->update("`openid` = '{$openid}'",array('vote'=>$voteitems));
		$vote_m=new M('vote');
		foreach ($voteitems_arr as $value) {
			$succed=$vote_m->update("`id` = '{$value}'","`res` =  `res`+1");
		}
		echo '{"errno":0,"message":"投票成功！"}';
		return ;
	}
}
function blackword($content, $xuanzezu) {
	if (! empty ( $xuanzezu ['black_word'] )) {
		$blackarr = explode ( ",", $xuanzezu ['black_word'] );
		
		foreach ( $blackarr as $v ) {
			if (strstr ( $content, $v )) {
				return 1;
			}
		}
		return 0;
	}
}
//judge=1 获取分数信息
//judge=2 获取人数
//judge=3 开始
//judge=4 结束
function hasmemcache($mem,$openid)
{
	// 		global $openid;
	$wall_state_key='wall_state';
	// echo $mem->get($wall_state_key);
	$wall_state=$mem->get($wall_state_key);
	if(!$wall_state){
		echo json_encode(array(
				'status' => 5,
				'point' => 0
		));
		$mem->quit();
		return;
	}
	if($wall_state['isopen']==2){
		$prefix = 'xianchang_';
		$data = $mem->get($prefix . $openid);
		if(!$data){//没有签到
			echo json_encode(array(
					'status' => 4,
					'point' => 0
			));
		}else if($data && $data['point']+1>$wall_state['endshake']){
			//活动结束
			$wall_state['isopen']=1;
			$data['point']=$wall_state['endshake'];
			$mem->set($wall_state_key,$wall_state);
			$mem->set($prefix.$openid,$data,3600);
			echo json_encode($data);
		}else{
			//增加一分
			$data['point']+=1;
			$mem->set($prefix.$openid,$data,3600);
			echo json_encode($data);
		}
	}else{
		echo json_encode(array(
				'status' => 3,
				'point' => 0
		));
	}
	$mem->quit();
}

function hasmysql($openid)
{
	$fzz=new CacheFactory(CACHEMODE);
	$cachename='shake_status';
	$status=$fzz->get($cachename);
	//缓存不存在说明活动数据出错，可以联系主持人重置游戏，或者开始游戏解决
	if(!$status){
		//提示活动不存在或者没有开始
		echo json_encode(array(
				'status' => 3,
				'point' => 0,
				'message'=>"活动不存在"
		));
		return;
	}

	//1表示未开始2表示开始3表示人满4表示结束
	if($status['status']==1){
		echo json_encode(array(
				'status' => 3,
				'point' => 0,
				'message'=>"活动没有开始"
		));
		return;
	}
	
	if($status['status']==4){
		echo json_encode(array(
				'status' => 3,
				'point' => 0,
				'message'=>"活动已经结束"
		));
		return;
	}
	$shake_toshake_m = new M('shake_toshake');
	$where='`openid`="' . $openid . '" and roundno='.$status['roundno'];
	$data = $shake_toshake_m->find($where, 'id,point');
// 	echo var_export($status);
	if(!$data){
		//人数满的情况
		if($status['status']==3){
// 			echo json_encode(array(
// 					'status' => 3,
// 					'point' => 0,
// 					'message'=>"参与人数已经满了，下次动作快一点哦。"
// 			));
// 			return;
			echo json_encode(array(
					'status' => 6,
					'started_at'=>$status['started_at'],
					'duration'=>$status['duration'],
					'point' => 0,
					'message'=>"参与人数已经满了，下次动作快一点哦。"
			));
			return;
		}
		if($status['started_at']+$status['duration']<time()){
			echo json_encode(array(
					'status' => 3,
					'point' => 0,
					'message'=>"活动已经结束"
			));
			return;
		}
		$flag_m=new M('flag');
		$myinfo=$flag_m->find('rentopenid="'.$openid.'"','nickname,avatar');
		$data=array(
				'nickname'=>$myinfo['nickname'],
				'openid'=>$openid,
				'point'=>1,
				'avatar'=>$myinfo['avatar'],
				'roundno'=>$status['roundno']
		);
		$result=$shake_toshake_m->add($data);
		return;
	}
	//已经正常参与活动的人，活动也在进行中
	//活动时间到
	if($status['started_at']+$status['duration']<time()){
		echo json_encode(array(
				'status' => 3,
				'point' => $data['point'],
				'message'=>"活动已经结束"
		));
		return;
	}
	$point=intval($data['point'])+1;
	$shake_toshake_m->update($where,'point='.$point);
	echo json_encode(array(
			'status' => 2,
			'point' => $point
	));
	return;
}