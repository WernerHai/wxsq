<?php 
//http://xc.wxmiao.com/wall/ajax_act_shake.php?action=start&roundno=0
require_once(dirname(__FILE__) . '/../common/function.php');
require_once(dirname(__FILE__) . '/../common/http_helper.php');
include(dirname(__FILE__) . '/../common/db.class.php');
require_once(dirname(__FILE__) . '/../common/session_helper.php');
include('biaoqing.php');
if (!isset($_SESSION['views']) || $_SESSION['views'] != true) {
	$_SESSION['views'] = false;

	echo '{"errno":-100,"message":"登录活动"}';
}

$action = $_GET['action'];

switch ($action){
	case 'start':
		startgame();
		break;
	case 'working':
		workingdata();
		break;
	case 'reset':
		resetgame();
		break;
}

function startgame(){
	$roundno=isset($_GET['roundno'])?intval($_GET['roundno']):0;
	$shake_config_m=new M('shake_config');
	$shake_config=$shake_config_m->find('1');
	if($roundno<intval($shake_config['currentroundno'])){
		echo '{"errno":-1,"message":"第'.($roundno+1).'"轮活动已经结束"}';
		return;
	}
	if($roundno>intval($shake_config['currentroundno'])){
		$shake_config['currentroundno']=$roundno;
		$shake_config['currentroundstatus']=1;
	}
	
	if($roundno>=intval($shake_config['maxround'])){
		echo '{"errno":-2,"message":"摇一摇活动所有轮次都已经结束了"}';
		return;
	}
	//当前轮次的状态 1表示未开始2表示开始3表示人满4表示结束
	$shakestatus=intval($shake_config['currentroundstatus']);
	if($shakestatus!=2 && $shakestatus!=3){
		//本轮活动已经结束
		if($shakestatus==4){
			echo '{"errno":-3,"message":"本轮活动已经结束"}';
			return;
		}
		//如果状态是1或者0，把活动状态更新到2
		//初始化活动缓存
		require_once(dirname(__FILE__) . '/../common/CacheFactory.php');
		$cache=new CacheFactory(CACHEMODE);
		$cachename='shake_status';
		$status=array('roundno'=>intval($shake_config['currentroundno']),//轮次号 0开始
				'started_at'=>time(),//开始时间time()
				'status'=>2,//当前轮次活动状态
				'duration'=>intval($shake_config['duration']),//当前轮次持续时间
				'maxplayers'=>intval($shake_config['maxplayers']),//最大参与人数
				'maxdisplayplayers'=>intval($shake_config['maxdisplayplayers']),//显示前20名
		);
		$cache->set($cachename,$status,3600);
		
		//更新数据库信息
// 		echo 'currentroundstatus=2 and currentroundno='.$shake_config['currentroundno'];
		$updatedata=array(
				'currentroundstatus'=>2,
				'currentroundno'=>$shake_config['currentroundno']
		);
		$shake_config_m->update('1',$updatedata);
		echo '{"errno":1,"message":"活动开始"}';
		return;
	}
	echo '{"errno":2,"message":"活动已经开始"}';
	return;
}
function workingdata(){
	$roundno=isset($_GET['roundno'])?intval($_GET['roundno']):0;
	require_once(dirname(__FILE__) . '/../common/CacheFactory.php');
	$cache=new CacheFactory(CACHEMODE);
	$cachename='shake_status';
	$status=$cache->get($cachename);
	$shake_config_m=new M('shake_config');
	if(!$status || !isset($status['roundno'])){
		$shake_config=$shake_config_m->find('1');
		$status=array('roundno'=>intval($shake_config['currentroundno']),//轮次号 0开始
				'started_at'=>time(),//开始时间time()
				'status'=>$shake_config['currentroundstatus'],//当前轮次活动状态
				'duration'=>intval($shake_config['duration']),//当前轮次持续时间
				'maxplayers'=>intval($shake_config['maxplayers']),//最大参与人数
				'maxdisplayplayers'=>intval($shake_config['maxdisplayplayers']),//显示前20名)
			);
		$cache->set($cachename,$status,3600);
	}
	//活动结束，更新状态，并返回，中奖人得奖的列表
	$shake_toshake_m=new M('shake_toshake');
	if($status['started_at']+$status['duration']<time()){
		if($status['status']!=4){
			$status['status']=4;
			$cache->set($cachename,$status,3600);
			$shake_config_m->update(1,'currentroundstatus='.$status['status']);
		}
		//指定轮次按摇晃次数从大到小获取用户前 显示最大人数（maxdisplayplayers）个数据
// 		$where='roundno='.$roundno.' order by point desc limit '.$status['maxdisplayplayers'];
		$where='roundno='.$roundno.' order by point desc,id asc limit '.$status['maxdisplayplayers'];
		$shake_toshake=$shake_toshake_m->select($where);
		$data=array(
				'errno'=>0,
				'status'=>$status['status'],
				'data'=>formatshakeinfo($shake_toshake,$status['started_at'],$status['duration'])
		);
		echo json_encode($data);
		return;
	}
	//活动没有结束
	//获取排名情况
	$shakecount=$shake_toshake_m->find('roundno='.$roundno,'*','count');
	if($shakecount>=$status['maxplayers']){
		if($status['status']!=3){
			$status['status']=3;
			$cache->set($cachename,$status,3600);
			//需要更新到数据库
			$shake_config_m->update(1,'currentroundstatus='.$status['status']);
		}
	}
// 	$where='roundno='.$roundno.' order by point desc limit '.$status['maxdisplayplayers'];
	//显示10个
	$where='roundno='.$roundno.' order by point desc limit 10';//.$status['maxdisplayplayers'];
	$shake_toshake=$shake_toshake_m->select($where);

	$data=array(
			'errno'=>0,
			'status'=>$status['status'],
			'data'=>formatshakeinfo($shake_toshake,$status['started_at'],$status['duration'])
	);
	echo json_encode($data);
	return;

}
function resetgame(){
	$roundno=isset($_GET['roundno'])?intval($_GET['roundno']):-1;
	
	require_once(dirname(__FILE__) . '/../common/CacheFactory.php');
	$cache=new CacheFactory(CACHEMODE);
	$cachename='shake_status';
	$status=$cache->delete($cachename);
// 	echo var_export($cache->get($cachename));
	if($roundno<0){
		echo '{"errno":-3,",message":"数据错误"}';
		return ;
	}
	$shake_config_m=new M('shake_config');
	$shake_config=$shake_config_m->find('1');
	$data=array(
			'currentroundno'=>$roundno,
			'currentroundstatus'=>1,
	);
	$where='1';
	$shake_config_m->update($where, $data);
	$shake_toshake_m=new M('shake_toshake');
	$where='roundno='.$roundno;
	$shake_toshake_m->delete($where);
	echo '{"errno":1,",message":"已经重置成功"}';
	return;
	
}

function formatshakeinfo($shakeinfo,$started_at,$duration){
	$shakedata=array();
	$maxpoint=0;
	if(count($shakeinfo)>0){
		$maxpoint=$shakeinfo[0]['point'];
	}
	
	$timepercent=(time()-$started_at)/$duration;
	$timepercent=$timepercent>1?1:$timepercent;
	foreach($shakeinfo as $item){
		$newitem['nick_name']=pack ( 'H*', $item['nickname'] );
		if($maxpoint>0){
			$progress=ceil($item['point']/$maxpoint*$timepercent*100);
		}else{
			$progress=1;
		}
		$newitem['progress']=$progress;
		$newitem['avatar']=$item['avatar'];
		$shakedata[]=$newitem;
	}
	return $shakedata;
}