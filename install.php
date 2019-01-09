<?php
/**
 * 微赢现场活动大屏幕系统系统
 * 
 */
@set_time_limit(0);
@set_magic_quotes_runtime(0);
error_reporting(0);
ob_start();
define('IA_ROOT', str_replace("\\", DIRECTORY_SEPARATOR, dirname(__FILE__)));
if ($_GET['res']) {
    $res = $_GET['res'];
    $reses = tpl_resources();
    if (array_key_exists($res, $reses)) {
        if ($res == 'css') {
            header('content-type:text/css');
        } else {
            header('content-type:image/png');
        }
        echo base64_decode($reses[$res]);
        exit();
    }
}
$actions = array('license', 'env', 'db', 'finish');
$action = $_COOKIE['action'];
$action = in_array($action, $actions) ? $action : 'license';
$ispost = strtolower($_SERVER['REQUEST_METHOD']) == 'post';

if (file_exists(IA_ROOT . '/data/install.lock') && $action != 'finish') {
    header('location: ./index.php');
    exit;
}

header('content-type: text/html; charset=utf-8');
if ($action == 'license') {
    if ($ispost) {
        setcookie('action', 'env');
        header('location: ?refresh');
        exit;
    }
    tpl_install_license();
}
if ($action == 'env') {
    if ($ispost) {
        setcookie('action', $_POST['do'] == 'continue' ? 'db' : 'license');
        header('location: ?refresh');
        exit;
    }
    $ret = array();
    $ret['server']['os']['value'] = php_uname();
    if (PHP_SHLIB_SUFFIX == 'dll') {
        $ret['server']['os']['remark'] = '建议使用 Linux 系统以提升程序性能';
        $ret['server']['os']['class'] = 'warning';
    }
    $ret['server']['sapi']['value'] = $_SERVER['SERVER_SOFTWARE'];
    if (PHP_SAPI == 'isapi') {
        $ret['server']['sapi']['remark'] = '建议使用 Apache 或 Nginx 以提升程序性能';
        $ret['server']['sapi']['class'] = 'warning';
    }
    $ret['server']['php']['value'] = PHP_VERSION;
    $ret['server']['dir']['value'] = IA_ROOT;
    if (function_exists('disk_free_space')) {
        $ret['server']['disk']['value'] = floor(disk_free_space(IA_ROOT) / (1024 * 1024)) . 'M';
    } else {
        $ret['server']['disk']['value'] = 'unknow';
    }
    $ret['server']['upload']['value'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow';

    $ret['php']['version']['value'] = PHP_VERSION;
    $ret['php']['version']['class'] = 'success';
    if (version_compare(PHP_VERSION, '5.4.0') == -1) {
        $ret['php']['version']['class'] = 'danger';
        $ret['php']['version']['failed'] = true;
        $ret['php']['version']['remark'] = 'PHP版本必须为 5.4.0 以上. <a href="h#">详情</a>';
    }
    $ret['php']['mysqli']['ok'] = function_exists('mysqli_close');
    $ret['php']['mysqli']['class'] = 'success';
    if($ret['php']['mysqli']['ok']){
        $ret['php']['mysqli']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
    }else{
        $ret['php']['mysqli']['class'] = 'danger';
        $ret['php']['mysqli']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
    }
    // $ret['php']['mysql']['ok'] = function_exists('mysql_connect');
    // if ($ret['php']['mysql']['ok']) {
    //     $ret['php']['mysql']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
    // } else {
    //     $ret['php']['mysql']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
    // }

    // $ret['php']['pdo']['ok'] = extension_loaded('pdo') && extension_loaded('pdo_mysql');
    // if ($ret['php']['pdo']['ok']) {
    //     $ret['php']['pdo']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
    //     $ret['php']['pdo']['class'] = 'success';
    //     if (!$ret['php']['mysql']['ok']) {
    //         $ret['php']['pdo']['remark'] = '您的PHP环境虽然不支持 mysql_connect, 但已经支持了PDO, 这样系统是可以正常高效运行的, 不需要额外处理. ';
    //     }
    // } else {
    //     if ($ret['php']['mysql']['ok']) {
    //         $ret['php']['pdo']['value'] = '<span class="glyphicon glyphicon-remove text-warning"></span>';
    //         $ret['php']['pdo']['class'] = 'warning';
    //         $ret['php']['pdo']['remark'] = '您的PHP环境不支持PDO, 但支持 mysql_connect, 这样系统虽然可以运行, 但还是建议你开启PDO以提升程序性能和系统稳定性. ';
    //     } else {
    //         $ret['php']['pdo']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
    //         $ret['php']['pdo']['class'] = 'danger';
    //         $ret['php']['pdo']['remark'] = '您的PHP环境不支持PDO, 也不支持 mysql_connect, 系统无法正常运行. ';
    //         $ret['php']['pdo']['failed'] = true;
    //     }
    // }

    $ret['php']['fopen']['ok'] = @ini_get('allow_url_fopen') && function_exists('fsockopen');
    if ($ret['php']['fopen']['ok']) {
        $ret['php']['fopen']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
    } else {
        $ret['php']['fopen']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
    }

    $ret['php']['curl']['ok'] = extension_loaded('curl') && function_exists('curl_init');
    if ($ret['php']['curl']['ok']) {
        $ret['php']['curl']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['php']['curl']['class'] = 'success';
        if (!$ret['php']['fopen']['ok']) {
            $ret['php']['curl']['remark'] = '您的PHP环境虽然不支持 allow_url_fopen, 但已经支持了cURL, 这样系统是可以正常高效运行的, 不需要额外处理. ';
        }
    } else {
        if ($ret['php']['fopen']['ok']) {
            $ret['php']['curl']['value'] = '<span class="glyphicon glyphicon-remove text-warning"></span>';
            $ret['php']['curl']['class'] = 'warning';
            $ret['php']['curl']['remark'] = '您的PHP环境不支持cURL, 但支持 allow_url_fopen, 这样系统虽然可以运行, 但还是建议你开启cURL以提升程序性能和系统稳定性. ';
        } else {
            $ret['php']['curl']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
            $ret['php']['curl']['class'] = 'danger';
            $ret['php']['curl']['remark'] = '您的PHP环境不支持cURL, 也不支持 allow_url_fopen, 系统无法正常运行. ';
            $ret['php']['curl']['failed'] = true;
        }
    }

    $ret['php']['ssl']['ok'] = extension_loaded('openssl');
    if ($ret['php']['ssl']['ok']) {
        $ret['php']['ssl']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['php']['ssl']['class'] = 'success';
    } else {
        $ret['php']['ssl']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
        $ret['php']['ssl']['class'] = 'danger';
        $ret['php']['ssl']['failed'] = true;
        $ret['php']['ssl']['remark'] = '没有启用OpenSSL, 将无法访问公众平台的接口, 系统无法正常运行. ';
    }

    $ret['php']['gd']['ok'] = extension_loaded('gd');
    if ($ret['php']['gd']['ok']) {
        $ret['php']['gd']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['php']['gd']['class'] = 'success';
    } else {
        $ret['php']['gd']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
        $ret['php']['gd']['class'] = 'danger';
        $ret['php']['gd']['failed'] = true;
        $ret['php']['gd']['remark'] = '没有启用GD, 将无法正常上传和压缩图片, 系统无法正常运行. ';
    }

    $ret['php']['dom']['ok'] = class_exists('DOMDocument');
    if ($ret['php']['dom']['ok']) {
        $ret['php']['dom']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['php']['dom']['class'] = 'success';
    } else {
        $ret['php']['dom']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
        $ret['php']['dom']['class'] = 'danger';
        $ret['php']['dom']['failed'] = true;
        $ret['php']['dom']['remark'] = '没有启用DOMDocument, 将无法正常安装使用模块, 系统无法正常运行. ';
    }

    $ret['write']['root']['ok'] = local_writeable(IA_ROOT . '/');
    if ($ret['write']['root']['ok']) {
        $ret['write']['root']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['write']['root']['class'] = 'success';
    } else {
        $ret['write']['root']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
        $ret['write']['root']['class'] = 'danger';
        $ret['write']['root']['failed'] = true;
        $ret['write']['root']['remark'] = '本地目录无法写入, 将无法使用自动更新功能, 系统无法正常运行. ';
    }
    $ret['write']['data']['ok'] = local_writeable(IA_ROOT . '/data');
    if ($ret['write']['data']['ok']) {
        $ret['write']['data']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['write']['data']['class'] = 'success';
    } else {
        $ret['write']['data']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
        $ret['write']['data']['class'] = 'danger';
        $ret['write']['data']['failed'] = true;
        $ret['write']['data']['remark'] = 'data目录无法写入, 将无法写入配置文件, 系统无法正常安装. ';
    }
    // $ret['write']['data']['ok'] = local_writeable(IA_ROOT . '/img');
    // if ($ret['write']['data']['ok']) {
    //     $ret['write']['data']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
    //     $ret['write']['data']['class'] = 'success';
    // } else {
    //     $ret['write']['data']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
    //     $ret['write']['data']['class'] = 'danger';
    //     $ret['write']['data']['failed'] = true;
    //     $ret['write']['data']['remark'] = 'img目录无法写入, 将无法写入配置文件, 系统无法正常安装. ';
    // }
    $ret['continue'] = true;
    foreach ($ret['php'] as $opt) {
        if ($opt['failed']) {
            $ret['continue'] = false;
            break;
        }
    }
    if ($ret['write']['failed']) {
        $ret['continue'] = false;
    }
    tpl_install_env($ret);
}
if ($action == 'db') {
    if ($ispost) {
        if ($_POST['do'] != 'continue') {
            setcookie('action', 'env');
            header('location: ?refresh');
            exit();
        }
        $family = $_POST['family'] == 'x' ? 'x' : 'v';
        $db = $_POST['db'];
        $user = $_POST['user'];
        // $link = mysql_connect($db['server'], $db['username'], $db['password']);
        $link = mysqli_connect($db['server'], $db['username'], $db['password']);
        if (empty($link)) {
            $error = mysqli_connect_error();
            if (strpos($error, 'Access denied for user') !== false) {
                $error = '您的数据库访问用户名或是密码错误. <br />';
            } else {
                $error = iconv('gbk', 'utf8', $error);
            }
        } else {
            // mysql_query("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary");
            mysqli_query($link,"SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary");
            // mysql_query("SET sql_mode=''");
            mysqli_query($link,"SET sql_mode=''");
            // if (mysql_error()) {
            //     $error = mysql_error();
            if (mysqli_error($link)) {
                $error = mysqli_error($link);
            } else {
                // $query = mysql_query("SHOW DATABASES LIKE  '{$db['name']}';");
                $query = mysqli_query($link,"SHOW DATABASES LIKE  '{$db['name']}';");
                if (!mysqli_fetch_assoc($query)) {
                    if (mysqli_get_server_info() > '4.1') {
                        mysqli_query($link,"CREATE DATABASE IF NOT EXISTS `{$db['name']}` DEFAULT CHARACTER SET utf8");
                    } else {
                        mysqli_query($link,"CREATE DATABASE IF NOT EXISTS `{$db['name']}`");
                    }
                }
                $query = mysqli_query($link,"SHOW DATABASES LIKE  '{$db['name']}';");
                if (!mysqli_fetch_assoc($query)) {
                    $error .= "数据库不存在且创建数据库失败. <br />";
                }
                if (mysqli_errno($link)) {
                    $error .= mysqli_error($link);
                }
            }
        }
        if (empty($error)) {
            mysqli_select_db($link,$db['name']);
            // $query = mysqli_query($link,"SHOW TABLES LIKE '{$db['prefix']}%';");
            // if (mysqli_fetch_assoc($query)) {
                // $error = '您的数据库不为空，请重新建立数据库或是清空该数据库或更改表前缀！';
            // }
        }
        if (empty($error)) {
            $pieces = explode(':', $db['server']);
            $db['port'] = !empty($pieces[1]) ? $pieces[1] : '3306';
            $config = local_config();
            $cookiepre = local_salt(4) . '_';
            $authkey = local_salt(8);
            $config = str_replace(array(
                '{db-server}', '{db-username}', '{db-password}', '{db-port}', '{db-name}', '{db-tablepre}', '{cookiepre}', '{authkey}', '{attachdir}'
            ), array(
                $db['server'], $db['username'], $db['password'], $db['port'], $db['name'], $db['prefix'], $cookiepre, $authkey, 'attachment'
            ), $config);
            $verfile = IA_ROOT . '/version.php';
            $dbfile = IA_ROOT . '/sql/db.php';

            if ($_POST['type'] == 'remote') {
                mysqli_close($link);
                $link = mysqli_connect($db['server'], $db['username'], $db['password']);
                mysqli_select_db($link,$db['name']);
                mysqli_query($link,"SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary");
                mysqli_query($link,"SET sql_mode=''");
                // mysqli_query($link,"use ".$db['name']);
                $version = $ins['version'];
                $release = $ins['release'];
                $family = $ins['family'];
                //写入临时目录
                $tmpfile = IA_ROOT . '/config.tmp';
                file_put_contents($tmpfile, $archive);
                file_put_contents(IA_ROOT . '/config.php', base64_decode($ins['schemas']));

                $fp = fopen($tmpfile, 'r');
                if ($fp) {
                    $buffer = '';
                    while (!feof($fp)) {
                        $buffer .= fgets($fp, 4096);
                        if ($buffer[strlen($buffer) - 1] == "\n") {
                            $pieces = explode(':', $buffer);
                            $path = base64_decode($pieces[0]);
                            $dat = base64_decode($pieces[1]);
                            $fname = IA_ROOT . $path;
                            local_mkdirs(dirname($fname));
                            file_put_contents($fname, $dat);
                            $buffer = '';
                        }
                    }
                    fclose($fp);
                }
                unlink($tmpfile);

                $verdat = <<<VER
<?php
/**
 * 版本号
 */		
defined('IN_IA') or exit('Access Denied');
define('FAMILY', 'v');
define('VERSION', '0.9');
define('RELEASE_DATE', '201512041120');
VER;
                file_put_contents($verfile, $verdat);
            }

            if (file_exists(IA_ROOT . '/index.php') && file_exists($verfile)) {
                $dat = require $dbfile;
                if (empty($dat) || !is_array($dat)) {
                    die('<script type="text/javascript">alert("安装包不正确, 数据安装脚本缺失.");history.back();</script>');
                }
				
                foreach ($dat['datas'] as $data) {
//                 	echo var_export($data);
                    local_run($data);
                }
            } else {
                die('<script type="text/javascript">alert("你正在使用本地安装, 但未下载完整安装包, 请从微信墙官网下载完整安装包后重试.");history.back();</script>');
            }

            $salt = local_salt(8);

            mysqli_query($link,"INSERT INTO weixin_admin(user, pwd) VALUES('{$user['username']}', '{$user['password']}')");
//             $web_root=$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
//             $web_root='http://'.str_replace('/install.php', '', $web_root);
//             mysqli_query($link,"UPDATE weixin_wall_config set web_root='".$web_root."'");
			//写入一个签到的随机码uniqid
			$verifycode=uniqid();
			$sql="update weixin_wall_config set verifycode='".$verifycode."' where 1=1";
			mysqli_query($link,$sql);
            local_mkdirs(IA_ROOT . '/data/images');
            file_put_contents(IA_ROOT . '/data/config.php', $config);
            touch(IA_ROOT . '/data/install.lock');
            file_put_contents(IA_ROOT . '/data/install.lock', time());
            setcookie('action', 'finish');
            header('location: ?refresh');
            exit();
        }
    }
    tpl_install_db($error);

}
if ($action == 'finish') {
    setcookie('action', '', -10);
    $dbfile = IA_ROOT . '/config.php';
    @unlink($dbfile);
    //header('location: /index.php');
    tpl_install_finish();

}

function local_writeable($dir)
{
    $writeable = 0;
    if (!is_dir($dir)) {
        @mkdir($dir, 0777);
    }
    if (is_dir($dir)) {
        if ($fp = fopen("$dir/test.txt", 'w')) {
            fclose($fp);
            unlink("$dir/test.txt");
            $writeable = 1;
        } else {
            $writeable = 0;
        }
    }
    return $writeable;
}

function local_salt($length = 8)
{
    $result = '';
    while (strlen($result) < $length) {
        $result .= sha1(uniqid('', true));
    }
    return substr($result, 0, $length);
}

function local_config()
{
    $cfg = <<<EOF
<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING);
@header("Content-type: text/html; charset=utf-8");
define('SYSTEMPATH',str_replace('data', '', dirname(__FILE__)));
define ( 'SAVEFILEMODE', 'file' );
/*链接数据库*/
\$dbname = '{db-name}';//数据库的名称
/*设置数据库信息*/
\$host = '{db-server}';//数据库的服务器地址，一般无需更改
\$port = '{db-port}';//数据库的端口号
\$user = '{db-username}';//数据库的用户名
\$pwd = '{db-password}';//数据库的密码
define('CACHEMODE','file');//数据库的密码
EOF;
    return trim($cfg);
}

function local_mkdirs($path)
{
    if (!is_dir($path)) {
        local_mkdirs(dirname($path));
        mkdir($path);
    }
    return is_dir($path);
}

function local_run($sql)
{
    global $link, $db;
    if (!isset($sql) || empty($sql)) return;
    $ret = array();
    $num = 0;
    foreach (explode(";|", trim($sql)) as $query) {
        $ret[$num] = '';
        $queries = explode("\n", trim($query));
        foreach ($queries as $query) {
            $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0] . $query[1] == '--') ? '' : $query;
        }
        $num++;
    }
    unset($sql);
//     echo var_export($ret);exit();
    foreach ($ret as $query) {
        $query = trim($query);
        if ($query) {
            if (!mysqli_query($link,$query)) {
                echo mysqli_errno($link) . ": " . mysqli_error($link) . "<br />";
                exit($query);
            }
        }
    }
}


function tpl_frame()
{
    global $action, $actions;
    $action = $_COOKIE['action'];
    $step = array_search($action, $actions);
    $steps = array();
    for ($i = 0; $i <= $step; $i++) {
        if ($i == $step) {
            $steps[$i] = ' list-group-item-info';
        } else {
            $steps[$i] = ' list-group-item-success';
        }
    }
    $progress = $step * 25 + 25;
    $content = ob_get_contents();
    if ($content) ob_end_clean();
    $tpl = <<<EOF
<!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>安装系统 - 微信墙</title>
		<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">
		<style>
			html,body{font-size:13px;font-family:"Microsoft YaHei UI", "微软雅黑", "宋体";}
			.pager li.previous a{margin-right:10px;}
			.header a{color:#FFF;}
			.header a:hover{color:#428bca;}
			.footer{padding:10px;}
			.footer a,.footer{color:#eee;font-size:14px;line-height:25px;}
		</style>
		<!--[if lt IE 9]>
		  <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		  <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body style="background-color:#28b0e4;">
		<div class="container">
			<div class="header" style="margin:15px auto;">
				<ul class="nav nav-pills pull-right" role="tablist">
					<li role="presentation" class="active"><a href="javascript:;">安装微信墙</a></li>
					<li role="presentation"><a href="http://shop35088558.taobao.com/">微信墙官网</a></li>
					
				</ul>
				<img src="http://images.veiying.cn/logo_landscape.png" style="height:60px;"/>
			</div>
			<div class="row well" style="margin:auto 0;">
				<div class="col-xs-3">
					<div class="progress" title="安装进度">
						<div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="{$progress}" aria-valuemin="0" aria-valuemax="100" style="width: {$progress}%;">
							{$progress}%
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							安装步骤
						</div>
						<ul class="list-group">
							<a href="javascript:;" class="list-group-item{$steps[0]}"><span class="glyphicon glyphicon-copyright-mark"></span> &nbsp; 许可协议</a>
							<a href="javascript:;" class="list-group-item{$steps[1]}"><span class="glyphicon glyphicon-eye-open"></span> &nbsp; 环境监测</a>
							<a href="javascript:;" class="list-group-item{$steps[2]}"><span class="glyphicon glyphicon-cog"></span> &nbsp; 参数配置</a>
							<a href="javascript:;" class="list-group-item{$steps[3]}"><span class="glyphicon glyphicon-ok"></span> &nbsp; 成功</a>
						</ul>
					</div>
				</div>
				<div class="col-xs-9">
					{$content}
				</div>
			</div>
			<div class="footer" style="margin:15px auto;">
				<div class="text-center">
					<a href="#">关于我们</a> &nbsp; &nbsp; <a href="#">帮助</a> &nbsp; &nbsp; <a href="#">购买授权</a>
				</div>
				<div class="text-center">
					Powered by <a href="#"><b>微信墙</b></a> v0.6 &copy; 2014 
				</div>
			</div>
		</div>
		<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
		<script src="http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	</body>
</html>
EOF;
    echo trim($tpl);
}

function tpl_install_license()
{
    echo <<<EOF
		<div class="panel panel-default">
			<div class="panel-heading">阅读许可协议</div>
			<div class="panel-body" style="overflow-y:scroll;max-height:400px;line-height:20px;">
				<h3>版权所有 (c)2014-2015，微信墙团队保留所有权利。 </h3>
				<p>
					访问者可将本网站提供的内容或服务用于个人学习、研究或欣赏,以及其他非商业性或非盈利性用途,但同时应遵守著作权法及其他相关法律的规定,不得侵犯本网站及相关权利人的合法权利。除此以外,将本网站任何内容或服务用于其他用途时,须征得本网站及相关权利人的书面许可,并支付报酬。
                    本网站内容原作者如不愿意在本网站刊登内容,请及时通知本站,予以删除。<br/>
地址:金帆街966号2号楼510<br/>
电话:0579-81319988<br/>
电子邮箱:181603816@qq.com
				</p>
			</div>
		</div>
		<form class="form-inline" role="form" method="post">
			<ul class="pager">
				<li class="pull-left" style="display:block;padding:5px 10px 5px 0;">
					<div class="checkbox">
						<label>
							<input type="checkbox"> 我已经阅读并同意此协议
						</label>
					</div>
				</li>
				<li class="previous"><a href="javascript:;" onclick="if(jQuery(':checkbox:checked').length == 1){jQuery('form')[0].submit();}else{alert('您必须同意软件许可协议才能安装！')};">继续 <span class="glyphicon glyphicon-chevron-right"></span></a></li>
			</ul>
		</form>
EOF;
    tpl_frame();
}

function tpl_install_env($ret = array())
{
    if (empty($ret['continue'])) {
        $continue = '<li class="previous disabled"><a href="javascript:;">请先解决环境问题后继续</a></li>';
    } else {
        $continue = '<li class="previous"><a href="javascript:;" onclick="$(\'#do\').val(\'continue\');$(\'form\')[0].submit();">继续 <span class="glyphicon glyphicon-chevron-right"></span></a></li>';
    }
    echo <<<EOF
		<div class="panel panel-default">
			<div class="panel-heading">服务器信息</div>
			<table class="table table-striped">
				<tr>
					<th style="width:150px;">参数</th>
					<th>值</th>
					<th></th>
				</tr>
				<tr class="{$ret['server']['os']['class']}">
					<td>服务器操作系统</td>
					<td>{$ret['server']['os']['value']}</td>
					<td>{$ret['server']['os']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['sapi']['class']}">
					<td>Web服务器环境</td>
					<td>{$ret['server']['sapi']['value']}</td>
					<td>{$ret['server']['sapi']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['php']['class']}">
					<td>PHP版本</td>
					<td>{$ret['server']['php']['value']}</td>
					<td>{$ret['server']['php']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['dir']['class']}">
					<td>程序安装目录</td>
					<td>{$ret['server']['dir']['value']}</td>
					<td>{$ret['server']['dir']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['disk']['class']}">
					<td>磁盘空间</td>
					<td>{$ret['server']['disk']['value']}</td>
					<td>{$ret['server']['disk']['remark']}</td>
				</tr>
				<tr class="{$ret['server']['upload']['class']}">
					<td>上传限制</td>
					<td>{$ret['server']['upload']['value']}</td>
					<td>{$ret['server']['upload']['remark']}</td>
				</tr>
			</table>
		</div>

		<div class="alert alert-info">PHP环境要求必须满足下列所有条件，否则系统或系统部份功能将无法使用。</div>
		<div class="panel panel-default">
			<div class="panel-heading">PHP环境要求</div>
			<table class="table table-striped">
				<tr>
					<th style="width:150px;">选项</th>
					<th style="width:180px;">要求</th>
					<th style="width:50px;">状态</th>
					<th>说明及帮助</th>
				</tr>
				<tr class="{$ret['php']['version']['class']}">
					<td>PHP版本</td>
					<td>5.4或者5.4以上</td>
					<td>{$ret['php']['version']['value']}</td>
					<td>{$ret['php']['version']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['mysqli']['class']}">
					<td>MySQLI</td>
					<td>支持</td>
					<td >{$ret['php']['mysqli']['value']}</td>
                    <td ></td>
				</tr>

				<tr class="{$ret['php']['curl']['class']}">
					<td>allow_url_fopen</td>
					<td>支持(建议支持cURL)</td>
					<td>{$ret['php']['fopen']['value']}</td>
					<td rowspan="2">{$ret['php']['curl']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['curl']['class']}">
					<td>cURL</td>
					<td>支持(强烈建议支持)</td>
					<td>{$ret['php']['curl']['value']}</td>
				</tr>
				<tr class="{$ret['php']['ssl']['class']}">
					<td>openSSL</td>
					<td>支持</td>
					<td>{$ret['php']['ssl']['value']}</td>
					<td>{$ret['php']['ssl']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['gd']['class']}">
					<td>GD2</td>
					<td>支持</td>
					<td>{$ret['php']['gd']['value']}</td>
					<td>{$ret['php']['gd']['remark']}</td>
				</tr>
				<tr class="{$ret['php']['dom']['class']}">
					<td>DOM</td>
					<td>支持</td>
					<td>{$ret['php']['dom']['value']}</td>
					<td>{$ret['php']['dom']['remark']}</td>
				</tr>
			</table>
		</div>

		<div class="alert alert-info">系统要求微信墙整个安装目录必须可写, 才能使用微信墙所有功能。</div>
		<div class="panel panel-default">
			<div class="panel-heading">目录权限监测</div>
			<table class="table table-striped">
				<tr>
					<th style="width:150px;">目录</th>
					<th style="width:180px;">要求</th>
					<th style="width:50px;">状态</th>
					<th>说明及帮助</th>
				</tr>
				<tr class="{$ret['write']['root']['class']}">
					<td>/</td>
					<td>整目录可写</td>
					<td>{$ret['write']['root']['value']}</td>
					<td>{$ret['write']['root']['remark']}</td>
				</tr>
				<tr class="{$ret['write']['data']['class']}">
					<td>/</td>
					<td>data目录可写</td>
					<td>{$ret['write']['data']['value']}</td>
					<td>{$ret['write']['data']['remark']}</td>
				</tr>
			</table>
		</div>
		<form class="form-inline" role="form" method="post">
			<input type="hidden" name="do" id="do" />
			<ul class="pager">
				<li class="previous"><a href="javascript:;" onclick="$('#do').val('back');$('form')[0].submit();"><span class="glyphicon glyphicon-chevron-left"></span> 返回</a></li>
				{$continue}
			</ul>
		</form>
EOF;
    tpl_frame();
}

function tpl_install_db($error = '')
{
    if (!empty($error)) {
        $message = '<div class="alert alert-danger">发生错误: ' . $error . '</div>';
    }
    $insTypes = array();
    if (file_exists(IA_ROOT . '/index.php') && is_dir(IA_ROOT . '/app') && is_dir(IA_ROOT . '/web')) {
        $insTypes['local'] = ' checked="checked"';
    } else {
        $insTypes['remote'] = ' checked="checked"';
    }
    if (!empty($_POST['type'])) {
        $insTypes = array();
        $insTypes[$_POST['type']] = ' checked="checked"';
    }
    $disabled = empty($insTypes['local']) ? ' disabled="disabled"' : '';
    echo <<<EOF
	{$message}
	<form class="form-horizontal" method="post" role="form">
		<!--<div class="panel panel-default">
			<div class="panel-heading">安装选项</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">安装方式</label>
					<div class="col-sm-10">
						
						<label class="radio-inline">
							<input type="radio" name="type" value="local"{$insTypes['local']}{$disabled}> 离线安装
						</label>
						<div class="help-block">
							在线安装能够直接安装最新版本微信墙系统, 如果在线安装困难, 请下载离线安装包后使用本地安装. <br/>
							离线安装包可能不是最新程序, 如果你不确定, 可以现在访问官网重新下载一份最新的.
						</div>
					</div>
				</div>
			</div>
		</div>-->
		<div class="panel panel-default">
			<div class="panel-heading">数据库选项</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">数据库主机</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[server]" value="127.0.0.1">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">数据库用户</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[username]" value="root">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">数据库密码</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[password]">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">数据库名称</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" name="db[name]" value="wxwall">
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">管理选项</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">管理员账号</label>
					<div class="col-sm-4">
						<input class="form-control" type="username" name="user[username]">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">管理员密码</label>
					<div class="col-sm-4">
						<input class="form-control" type="password" name="user[password]">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">确认密码</label>
					<div class="col-sm-4">
						<input class="form-control" type="password"">
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="do" id="do" />
		<ul class="pager">
			<li class="previous"><a href="javascript:;" onclick="$('#do').val('back');$('form')[0].submit();"><span class="glyphicon glyphicon-chevron-left"></span> 返回</a></li>
			<li class="previous"><a href="javascript:;" onclick="if(check(this)){jQuery('#do').val('continue');$('form')[0].submit();}">继续 <span class="glyphicon glyphicon-chevron-right"></span></a></li>
		</ul>
	</form>
	<script>
		var lock = false;
		function check(obj) {
			if(lock) {
				return;
			}
			$('.form-control').parent().parent().removeClass('has-error');
			var error = false;
			$('.form-control').each(function(){
				if($(this).val() == '') {
					$(this).parent().parent().addClass('has-error');
					this.focus();
					error = true;
				}
			});
			if(error) {
				alert('请检查未填项');
				return false;
			}
			if($(':password').eq(0).val() != $(':password').eq(1).val()) {
				$(':password').parent().parent().addClass('has-error');
				alert('确认密码不正确.');
				return false;
			}
			lock = true;
			$(obj).parent().addClass('disabled');
			$(obj).html('正在执行安装');
			return true;
		}
	</script>
EOF;
    tpl_frame();
}

function tpl_install_finish()
{
    echo <<<EOF
	<div class="page-header"><h3>安装完成</h3></div>
	<div class="alert alert-success">
		恭喜您!已成功安装“现场活动大屏幕系统”系统，您现在可以: <br /><a target="_blank" class="btn btn-success" href="/index.php">访问网站首页</a><a target="_blank" class="btn btn-success" href="/myadmin">访问网站后台</a>
	</div>
EOF;
    tpl_frame();
}

function tpl_resources()
{
    static $res = array(
        'logo' => 'http://images.veiying.cn/logo_landscape.png',
    );
    return $res;
}
