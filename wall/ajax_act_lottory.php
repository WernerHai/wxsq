<?php
require_once(dirname(__FILE__) . '/../common/function.php');
require_once(dirname(__FILE__) . '/../common/http_helper.php');
include(dirname(__FILE__) . '/../common/db.class.php');
include('biaoqing.php');
$action = $_GET['action'];
$flag_m=new M('flag');
$wall_config_m = new M('wall_config');
$wall_config=$wall_config_m->find('1');
switch($action){
	case 'reset':
		gamereset();
		break;
	case 'ready':
		gameready();
		break;
	case 'ok':
		gameok();
		break;
}
function gamereset(){
	$awardid=isset($_POST['awardid'])?intval($_POST['awardid']):0;
	$from=isset($_POST['from'])?strval($_POST['from']):'';
	if($awardid==0){
		echo '{"ret":-2,"message":"奖品信息错误"}';
		return;
	}
	if($from!=''){
		$from_where=" and fromplug='".$from."'";
	}
	
	$zjlist_m=new M('zjlist');
	//删除没有内定的人的信息
	$zjlist_m->delete('awardid='.$awardid.$from_where.' and (status=2 or status=3) and (designated is NULL or designated=1)');
	//重置内定人的中奖状态
	$zjlist_m->update('awardid='.$awardid.$from_where.' and (status=2 or status=3) and designated=2 or designated=3',array('status'=>1));
	echo '{"ret":1,"message":"获奖信息重置成功"}';
	return;
}

function gameready(){
	$awardid=isset($_GET['awardid'])?intval($_GET['awardid']):0;
	$from=isset($_GET['from'])?strval($_GET['from']):'cj';
	if($awardid<=0){
		echo '{"ret":-1,"message":"请选择一个奖品再进行抽奖"}';
		return;
	}
	$showtype_array=array('cj'=>'cjshowtype','cjx'=>'cjxshowtype','zjd'=>'zjdshowtype','threedimensionallottery'=>'threedimensionallotteryshowtype');
	$system_config_m = new M('system_config');
	$showtype=$system_config_m->find('configkey="'.$showtype_array[$from].'"');
	$pagesize=100;
	$flag_m=new M('flag');
	$countsql=countsql($from,$awardid);
	$result=$flag_m->query($countsql);
	$data=$flag_m->fetch_array($result);
	$flagcount=isset($data[0])?$data[0]['cnt']:0;
	$view_cjperson_sql=listpersonsql($from,$awardid,$pagesize);
	$result=$flag_m->query($view_cjperson_sql);
	$view_cjpersonlist=$flag_m->fetch_array($result);
	foreach($view_cjpersonlist as $row1){
		$nickname='';
		if($showtype['configvalue']==2 && !empty($row1['signname'])){
			//显示姓名
			$nickname=$row1['signname'];
		}
		if($showtype['configvalue']==3 && !empty($row1['phone'])){
			//显示电话
			$nickname=substr_replace($row1['phone'],'****',3,4);
		}
		if($showtype['configvalue']==1 || empty($nickname)){
			$nickname = pack('H*', $row1['nickname']);
			$nickname = emoji_unified_to_html(emoji_softbank_to_unified($nickname));
		}
		$arr[] = array(
				'openid' => $row1['openid'],
				'avatar' => $row1['avatar'],
				'nick_name' => $nickname,
				'designated'=>$row1['designated']
		);
	}
	$view_zjlist_m=new M('view_zjlist');
	$zjlist_where='(status=2 or status=3) and awardid='.$awardid.' and fromplug ="'.$from.'"';
	$view_zjlist=$view_zjlist_m->select($zjlist_where);
	foreach($view_zjlist as $row1){
		$nickname='';
		if($showtype['configvalue']==2 && !empty($row1['signname'])){
			//显示姓名
			$nickname=$row1['signname'];
		}
		if($showtype['configvalue']==3 && !empty($row1['phone'])){
			//显示电话
			$nickname=substr_replace($row1['phone'],'****',3,4);
		}
		if($showtype['configvalue']==1 || empty($nickname)){
			$nickname = pack('H*', $row1['nickname']);
			$nickname = emoji_unified_to_html(emoji_softbank_to_unified($nickname));
		}
		$zjlistarr[] = array(
				'openid'=>$row1['openid'],
				'avatar' => $row1['avatar'],
				'nick_name' => $nickname,
				'designated'=>$row1['designated']
		);
	}
	$result_arr=array('count'=>$flagcount,'data'=>$arr,'luckuser'=>$zjlistarr,'ret'=>0);
	echo json_encode($result_arr);
	exit();
}


function gameok(){
	$awardid=isset($_POST['awardid'])?intval($_POST['awardid']):0;
	$from=isset($_POST['from'])?strval($_POST['from']):'cj';
	if($awardid<=0){
		echo '{"ret":-2,"message":"奖品信息错误"}';
		return;
	}
	$zjlist_m=new M('zjlist');
	$zjperson=$zjlist_m->find('fromplug="'.$from.'" and awardid='.$awardid.' and designated=2 and (weixin_zjlist.status!=2 and weixin_zjlist.status!=3)','weixin_zjlist.openid,weixin_flag.nickname,weixin_flag.avatar,weixin_flag.signname,weixin_flag.phone,weixin_zjlist.designated',
			'','assoc',
			'left join weixin_flag on weixin_flag.openid=weixin_zjlist.openid');
	$showtype_array=array('cj'=>'cjshowtype','cjx'=>'cjxshowtype','zjd'=>'zjdshowtype','threedimensionallottery'=>'threedimensionallotteryshowtype');
	$system_config_m = new M('system_config');
	$showtype=$system_config_m->find('configkey="'.$showtype_array[$from].'"');
	if($zjperson!=null){
		$result=setzjperson($zjperson['openid'],$awardid,$from);
		$zjperson['nick_name']=pack('H*', $zjperson['nickname']);
		if($showtype['configvalue']==2 && !empty($zjperson['signname'])){
			//显示姓名
			$zjperson['nick_name']=$zjperson['signname'];
		}
		if($showtype['configvalue']==3 && !empty($zjperson['phone'])){
			//显示电话
			$zjperson['nick_name']=substr_replace($zjperson['phone'],'****',3,4);
		}
		
		
		if($result){
			$returndata=array("ret"=>1,"message"=>"抽奖操作成功","data"=>$zjperson);
			echo json_encode($returndata);
			return;
		}else{
			echo '{"ret":-3,"message":"抽奖过程出现错误"}';
			return;
		}
	}else{
		$flag_m=new M('flag');		
		$data=$flag_m->find('flag=2 and weixin_flag.openid not in(select weixin_zjlist.openid from weixin_zjlist where fromplug="'.$from.'"  and ((weixin_zjlist.status=2 or weixin_zjlist.status=3) or (awardid='.$awardid.' and designated=3) or designated=2)) order by rand() limit 1',
				'weixin_flag.openid,weixin_flag.nickname,weixin_flag.avatar,weixin_flag.signname,weixin_flag.phone','',
				'assoc');
		$result=setzjperson($data['openid'],$awardid,$from);
		if($result){
			$zjperson=$data;
			$zjperson['nick_name']=pack('H*', $zjperson['nickname']);
			
			if($showtype['configvalue']==2 && !empty($zjperson['signname'])){
				//显示姓名
				$zjperson['nick_name']=$zjperson['signname'];
			}
			if($showtype['configvalue']==3 && !empty($zjperson['phone'])){
				//显示电话
				$zjperson['nick_name']=substr_replace($zjperson['phone'],'****',3,4);
			}
			
			$returndata=array("ret"=>1,"message"=>"抽奖操作成功","data"=>$zjperson);
			echo json_encode($returndata);
			return;
		}else{
			echo '{"ret":-3,"message":"抽奖过程出现错误"}';
			return;
		}
		return;
	}

}
//
function setzjperson($openid,$awardid,$from){
	$zjlist_m=new M('zjlist');
	$where='openid="'.$openid.'" and awardid='.$awardid.' and fromplug="'.$from.'"';
	$zjperson=$zjlist_m->find($where);
	$data=array(
			'status'=>2,
			'zjdatetime'=>time(),
			'verifycode'=>uniqid('wxq')
	);
	if(!$zjperson){
		$data['fromplug']=$from;
		$data['openid']=$openid;
		$data['awardid']=$awardid;
		$return=$zjlist_m->add($data);
	}else{
		$return=$zjlist_m->update($where, $data);
	}
	return $return;
}

function listpersonsql($from,$awardid,$limit=200){
	$sql='select * from (select `weixin_flag`.`openid` AS `openid`,
	`weixin_flag`.`flag` AS `flag`,
	`weixin_flag`.`nickname` AS `nickname`,
	`weixin_flag`.`avatar` AS `avatar`,
	`weixin_flag`.`content` AS `content`,
	`weixin_flag`.`status` AS `status`,
	`weixin_flag`.`datetime` AS `datetime`,
	`weixin_flag`.`phone` AS `phone`,
	`weixin_flag`.`signname` AS `signname`,
	`zjlist`.`awardid` AS `awardid`,
	`zjlist`.`designated` AS `designated`
	from(`weixin_flag`
			left join  (select * from `weixin_zjlist` where `fromplug` = "'.$from.'" ) as zjlist
			on((`zjlist`.`openid`= `weixin_flag`.`openid`)))
			where((`weixin_flag`.`status`= 1) and (`weixin_flag`.`flag`= 2)
					and(isnull(`zjlist`.`status`)
							or(`zjlist`.`status`= 1))
					and(isnull(`zjlist`.`designated`)
							or(`zjlist`.`designated`= 1)
							or(`zjlist`.`designated`= 2))
					)
					order by `zjlist`.`designated` desc)as resulttable where awardid IS NULL or awardid='.$awardid.' limit '.$limit;
	return $sql;
}

function countsql($from,$awardid){
	$sql='select count(0) as cnt from (select `weixin_flag`.`openid` AS `openid`,
	`weixin_flag`.`flag` AS `flag`,
	`weixin_flag`.`status` AS `status`,
	`weixin_flag`.`datetime` AS `datetime`,
	`zjlist`.`awardid` AS `awardid`,
	`zjlist`.`designated` AS `designated`
	from(`weixin_flag`
			left join  (select * from `weixin_zjlist` where `fromplug` = "'.$from.'" ) as zjlist
			on((`zjlist`.`openid`= `weixin_flag`.`openid`)))
			where((`weixin_flag`.`status`= 1) and (`weixin_flag`.`flag`= 2)
					and(isnull(`zjlist`.`status`)
							or(`zjlist`.`status`= 1))
					and(isnull(`zjlist`.`designated`)
							or(`zjlist`.`designated`= 1)
							or(`zjlist`.`designated`= 2))
					)
					order by `zjlist`.`designated` desc) as resulttable where awardid IS NULL or awardid='.$awardid;
	return $sql;
}
function startindex($count,$pagesize){
	$mod=$count%$pagesize;
	if($mod==$count){
		return 0;
	}
	$floor=floor($count/$pagesize);
	$start=rand(0,$floor);
	if($start==$floor){
		$start=$count-$pagesize;
	}else{
		$start=$pagesize*$start;
	}
	return $start;
}
?>