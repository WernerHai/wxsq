<?php
// error_reporting(E_ALL);
header("Content-type: text/html; charset=utf-8");
include(dirname(__FILE__) . '/../common/db.class.php');
// include("db.php");
$posts = $_POST;

foreach ($posts as $k => $v) {
	$posts[$k] = trim($v);
}
$pwd = $posts["userpwd"];
$username = $posts["username"];
$admin_m=new M('admin');
$userinfo=$admin_m->find("`user` = '{$username}' AND  `pwd` =  '{$pwd}'");
// echo var_export($userinfo);

if (!empty($userinfo)) {
	require_once('../common/session_helper.php');
	$_SESSION['admin'] = true;
	// echo $_SESSION[realpath("..") . 'admin'];
	// die();
	header("location:index.php");
} else {
	echo "用户或密码错误";
	echo '<a href="login.php">点击这里返回</a>';
	$str = <<<eot
<script language="javascript" type="text/javascript">
setTimeout("javascript:location.href='login.php'", 3000);
</script>
eot;
	echo "$str";
}
