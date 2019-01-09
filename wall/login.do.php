<?php
@header("Content-type: text/html; charset=utf-8");
include(dirname(__FILE__) . '/../common/db.class.php');
$wall_config_m = new M('wall_config');
$wall_config= $wall_config_m->find('1', '*', '');

$password=$_GET['password'];
// if (!$bakurl) {
//     $bakurl = 'index.php';
// }
// require 'db.php';
if($password==$wall_config['screenpaw']){
	require_once('../common/session_helper.php');
	$_SESSION['views']= true;
	echo '{"errno":0}';//errno
}