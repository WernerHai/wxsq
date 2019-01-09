<?php
require_once('db.class.php');
require_once('utilities.php');
if(interface_exists('SessionHandlerInterface')){
	class MySessionHandler implements SessionHandlerInterface{
		var $session_m;
		function open($save_path,$sessionName){
			// echo 'test';
			$this->session_m=new M('sessions');
			return true;
		}
		function close(){
			return true;
		}
		function read($id){
			$data=$this->session_m->find('session_id="'.$id.'"');
			if($data){
				return $data['user_data'];
			}
			return false;
		}
		
		function write($id,$data){
			$old_session_data=$this->session_m->find('session_id="'.$id.'"');
			// echo 'session_id='.$id;
			// echo var_export($old_session_data);
			if(!$old_session_data){
				$session_data=array(
						'session_id'=>$id,
						'ip_address'=>GetIp(),
						'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
						'last_activity'=>time(),
						'user_data'=>$data
				);
				
				$return=$this->session_m->add($session_data);
				return $return>0?true:false;
			}else{
				$session_data=array(
						'ip_address'=>GetIp(),
						'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
						'last_activity'=>time(),
						'user_data'=>$data
				);
				return $this->session_m->update('session_id="'.$id.'"',$session_data);
			}
		}
		function destroy($id){
			return $this->session_m->delete('session_id="'.$id.'"');
		}
		function gc($maxlifetime){
			return $this->session_m->delete('last_activity<'.(time()-$maxlifetime));
		}
	}
	ini_set('session.save_handler', 'user');
	$dbsessionhandler=new MySessionHandler();
	//传入实例的方式，可能在一些环境中无法正常运行，
	//session_set_save_handler($dbsessionhandler,true);  
	//还是使用保守的session_set_save_handler调用方式
	session_set_save_handler(array($dbsessionhandler,'open'),
			array($dbsessionhandler,'close'),
			array($dbsessionhandler,'read'),
			array($dbsessionhandler,'write'),
			array($dbsessionhandler,'destroy'),
			array($dbsessionhandler,'gc'));  
}
session_start();
/*
CREATE TABLE `tb_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '0',
  `user_agent` varchar(200) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
*/