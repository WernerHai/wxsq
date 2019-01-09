<?php 
include(dirname(__FILE__) . '/../common/db.class.php');
require_once(dirname(__FILE__) . '/../common/url_helper.php');
require_once(dirname(__FILE__) . '/../common/phpqrcode/qrlib.php');
require_once(dirname(__FILE__) . '/../common/Attachment_helper.php');
require_once(dirname(__FILE__) . '/../common/function.php');
$wall_config= getWallConf();
$mobileurl=request_scheme().'://'.$_SERVER['HTTP_HOST'].'/mobile/index.php?vcode='.$wall_config['verifycode'];
$width='300';
QRcode::png($mobileurl,false,QR_ECLEVEL_Q,10,2);