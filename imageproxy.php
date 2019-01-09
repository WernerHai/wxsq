<?php
require_once(dirname(__FILE__) . '/common/db.class.php');
require_once(dirname(__FILE__) . '/common/Attachment_helper.php');
require_once(dirname(__FILE__) . '/common/File_helper.php');
$imageid=$_GET['id'];
$fileinfo=GetAttachmentById($imageid);

switch ($fileinfo['type']){
	case 1:
		showlocalfile($fileinfo['filepath']);
		break;
	case 2:
		showremotefile($fileinfo['filepath']);
		break;
	default:
		break;
}

function showlocalfile($path){
	$imagepath=dirname(__FILE__).$path;
	$mime= get_mime_by_extension($imagepath); 
	$image = file_get_contents($imagepath);
	header('Content-type: '.$mime);
	echo $image;
}
function showremotefile($path){
	$mime= get_mime_by_extension($path);
	$image = file_get_contents($path);
	header('Content-type: '.$mime);
	echo $image;
}
// $image = file_get_contents($fileinfo['filepath']);  //假设当前文件夹已有图片001.jpg
// echo var_export($image);
// $content=addslashes($image);
// header('Content-type: image/jpg');
// echo $content;
// echo var_export($fileinfo);