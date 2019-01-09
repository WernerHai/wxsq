<?php
@header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once('../common/session_helper.php');
$action=$_GET['action'];
switch ($action){
	case 'saveredpacketround':
		saveredpacketround();
		break;
	case 'getredpacketround':
		getredpacketround();
		break;
	case 'delredpacketround':
		delredpacketround();
		break;
}
//删除一个红包轮次数据
function delredpacketround(){
	$id=isset($_POST['id'])?intval($_POST['id']):0;
	//已经有人中奖的活动，无法被删除
	$redpacket_users_m=new M('redpacket_users');
	$count=$redpacket_users_m->find('roundid='.$id.' and userid>0','*','count');
	if($count>0){
		$resultdata=array('code'=>-2,'message'=>'有人中奖的活动不能被删除。您可以另外再开一轮红包雨活动。');
		echo json_encode($resultdata);
		return;
	}
	$redpacket_round_m=new M('redpacket_round');
	$result=$redpacket_round_m->delete('id='.$id);
	
	//删除这一轮的红包数据
// 	$redpacket_users_m=new M('redpacket_users');
	$redpacket_users_m->delete('roundid='.$id);
	
	if($result){
		$resultdata=array('code'=>1,'message'=>'删除成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-1,'message'=>'删除失败');
		echo json_encode($resultdata);
		return;
	}
}
//保存一个红包轮次数据
function saveredpacketround(){
	$id=isset($_POST['id'])?intval($_POST['id']):0;
	$status=isset($_POST['status'])?intval($_POST['status']):1;
	$type=isset($_POST['type'])?intval($_POST['type']):1;
	$amount=isset($_POST['amount'])?intval($_POST['amount']*100):0;
	$num=isset($_POST['num'])?intval($_POST['num']):0;
	$numperperson=isset($_POST['numperperson'])?intval($_POST['numperperson']):1;
	$chance=isset($_POST['chance'])?intval($_POST['chance']*1000):0;
	$lefttime=isset($_POST['lefttime'])?intval($_POST['lefttime']):0;
	$minamount=isset($_POST['minamount'])?intval($_POST['minamount']*100):0;
	$maxamount=isset($_POST['maxamount'])?intval($_POST['maxamount']*100):0;
	$redpacket_round_m=new M('redpacket_round');
	$data=array('status'=>$status,'type'=>$type,'amount'=>$amount,'num'=>$num,
			'numperperson'=>$numperperson,'chance'=>$chance,'lefttime'=>$lefttime,
			'minamount'=>$minamount,'maxamount'=>$maxamount
	);
	if($chance<1){
		$resultdata=array('code'=>-10,'message'=>'获奖概率不能小于千分之一哦。');
		echo json_encode($resultdata);
		return;
	}
	if($type==1){//普通红包
		if($amount<100){
			$resultdata=array('code'=>-4,'message'=>'红包金额不能小于1元');
			echo json_encode($resultdata);
			return;
		}
		if($amount>20000){
			$resultdata=array('code'=>-5,'message'=>'红包金额不能大于200元');
			echo json_encode($resultdata);
			return;
		}
	}else{
		if($amount<100 || $amount<$num*$minamount){
			$resultdata=array('code'=>-6,'message'=>'红包总金额太小，不够分哦');
			echo json_encode($resultdata);
			return;
		}
		if($amount>$num*$maxamount){
			$resultdata=array('code'=>-9,'message'=>'红包总金额太大了，每个红包都是最大金额，还有多余哦');
			echo json_encode($resultdata);
			return;
		}
		if($minamount<100 || $maxamount<100){
			$resultdata=array('code'=>-7,'message'=>'红包金额不能小于1元');
			echo json_encode($resultdata);
			return;
		}
		if($minamount>200000 || $maxamount>20000){
			$resultdata=array('code'=>-8,'message'=>'红包金额不能大于200元');
			echo json_encode($resultdata);
			return;
		}
	}
	if($id>0){
		//修改数据，如果已经有人中奖，那么这轮数据除了可以修改活动状态以外不能修改其他信息
		$redpacket_users_m=new M('redpacket_users');
		$count=$redpacket_users_m->find('roundid='.$id.' and userid>0','*','count');
		if($count>0){
			$result=$redpacket_round_m->update('id='.$id,array('status'=>$data['status']));
			$resultdata=array('code'=>-11,'message'=>'您修改活动状态已经成功，其他数据无法修改，因为已经有人中奖了。');
			echo json_encode($resultdata);
			return;
		}else{
			//删除这一轮的红包数据
			$redpacket_users_m->delete('roundid='.$id);
			if($type==1){//普通红包
				updateredpacketusers($amount,$num,$id);
			}else{//随机红包
				updateredpacketusers($amount,$num,$id,$minamount,$maxamount);
			}
			$result=$redpacket_round_m->update('id='.$id,$data);
		}
	}else{
		$result=$redpacket_round_m->add($data);
		if($result>0){
			$redpacket_users_m=new M('redpacket_users');
			$redpacket_users_m->delete('roundid='.$result);
			if($type==1){//普通红包
				updateredpacketusers($amount,$num,$result);
			}else{//随机红包
				updateredpacketusers($amount,$num,$result,$minamount,$maxamount);
			}
		}
	}
	if($result){
		$resultdata=array('code'=>1,'message'=>'保存成功');
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-2,'message'=>'保存失败');
		echo json_encode($resultdata);
		return;
	}
	
	
}
//初始化所有红包
function updateredpacketusers($amount,$num,$roundid,$minamount=0,$maxmount=0){
	$created_at=time();
	$redpacket_users_m=new M('redpacket_users');
	if($minamount==0 || $maxmount==0){
		//普通红包
		
		for($i=0;$i<$num;$i++){
			$data=array('roundid'=>$roundid,'amount'=>$amount,'created_at'=>$created_at);
			$redpacket_users_m->add($data);
		}
	}else{
		//随机红包
		$data=array();
		$left=$amount;
		for($i=0;$i<$num;$i++){
			$data[$i]=$minamount;
			$left=$left-$minamount;
		}
		//金额可变的范围
		$delta=$maxmount-$minamount;
		//剩余金额大于0时分配金额
		while($left>0){
			for($i=0;$i<$num;$i++){
				if($left>0){
					//当次分配的金额
					$deltaamount=rand(0,$delta);
					if($left-$deltaamount<0){//分配的金额不能超过剩余的金额
						$deltaamount=$left;
						$left=0;
					}else{
						$left=$left-$deltaamount;
					}
					if($data[$i]+$deltaamount>$maxmount){//当次的金额加上当次增加的金额不能超出单次红包最大金额
						$left=$left+($data[$i]+$deltaamount-$maxmount);
						$data[$i]=$maxmount;
					}else{
						$data[$i]=$data[$i]+$deltaamount;
					}
				}
			}
		}
		
		for($i=0;$i<$num;$i++){
			$newdata=array('roundid'=>$roundid,'amount'=>$data[$i],'created_at'=>$created_at);
			$redpacket_users_m->add($newdata);
		}
	}
	
}
//按id获取红包轮次数据
function getredpacketround(){
	$id=isset($_POST['id'])?intval($_POST['id']):0;
	if($id==0){
		$resultdata=array('code'=>-1,'message'=>'ID错误');
		echo json_encode($resultdata);
		return;
	}
	$redpacket_round_m=new M('redpacket_round');
	$redpacket_round=$redpacket_round_m->find(' id='.$id);
	
	if(!empty($redpacket_round)){
		
		$redpacket_round['amount']=$redpacket_round['amount']/100;
		$redpacket_round['minamount']=$redpacket_round['minamount']/100;
		$redpacket_round['maxamount']=$redpacket_round['maxamount']/100;
		$redpacket_round['chance']=$redpacket_round['chance']/1000;
		
		$resultdata=array('code'=>1,'message'=>'获取数据成功','data'=>$redpacket_round);
		echo json_encode($resultdata);
		return;
	}else{
		$resultdata=array('code'=>-2,'message'=>'数据不存在');
		echo json_encode($resultdata);
		return;
	}
}
