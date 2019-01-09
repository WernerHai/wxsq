<?php

//获取微信配置信息
if (!function_exists('getWeixinConf')) {
    function getWeixinConf()
    {
        $weixin_configc = new M('weixin_config');
        $weixin_config = $weixin_configc->find();
        if($weixin_config['erweima']>0){
        	$erweima=GetAttachmentById($weixin_config['erweima']);
        	$weixin_config['erweima']=$erweima['type']==1?$erweima['filepath']:'/imageproxy.php?id='.$erweima['id'];
        }else{
        	$weixin_config['erweima']='';
        }
        
        $path=str_replace(DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'function.php', '', __FILE__);
        
        $apiclient_cert=GetAttachmentById($weixin_config['apiclient_cert']);
        $weixin_config['apiclient_cert']=$apiclient_cert['type']==1?($path.$apiclient_cert['filepath']):writecert($apiclient_cert['filepath'],'cert.pem');
        
        $apiclient_key=GetAttachmentById($weixin_config['apiclient_key']);
        $weixin_config['apiclient_key']=$apiclient_key['type']==1?($path.$apiclient_key['filepath']):writecert($apiclient_key['filepath'],'key.pem');
        
        $rootca=GetAttachmentById($weixin_config['rootca']);
        $weixin_config['rootca']=$rootca['type']==1?($path.$rootca['filepath']):writecert($rootca['filepath'],'rootca.pem');
        
        return $weixin_config;
    }
}
if (!function_exists('writecert')) {
	function writecert($url,$certname){
		if(empty($url))return '';
		$path=str_replace('common'.DIRECTORY_SEPARATOR.'function.php', 'data'.DIRECTORY_SEPARATOR, __FILE__);
		$filepath=$path.$certname;
		if(!file_exists($filepath)){
			$myfile = fopen($filepath, "w") or die("Unable to open file!");
			$certcontent=file_get_contents($url);
			fwrite($myfile, $certcontent);
			fclose($myfile);
		}
		return $filepath;
	}
}
//上墙配置
if (!function_exists('getWallConf')) {
    function getWallConf()
    {
        $wall_configc = new M('wall_config');
        $wall_config = $wall_configc->find();
        if($wall_config['bgimg']>0){
        	$bgimg=GetAttachmentById($wall_config['bgimg']);
        	$wall_config['bgimg']=$bgimg['type']==1?$bgimg['filepath']:'/imageproxy.php?id='.$bgimg['id'];
        }else{
        	$wall_config['bgimg']='';
        }
        
        if($wall_config['logoimg']>0){
        	$bgimg=GetAttachmentById($wall_config['logoimg']);
        	$wall_config['logoimg']=$bgimg['type']==1?$bgimg['filepath']:'/imageproxy.php?id='.$bgimg['id'];
        }else{
        	$wall_config['logoimg']='';
        }
        if($wall_config['bottom_logoimg']>0){
        	$bgimg=GetAttachmentById($wall_config['bottom_logoimg']);
        	$wall_config['bottom_logoimg']=$bgimg['type']==1?$bgimg['filepath']:'/imageproxy.php?id='.$bgimg['id'];
        }else{
        	$wall_config['bottom_logoimg']='';
        }
        return $wall_config;
    }
}
//弹幕配置
if (!function_exists('getDanmuConf')) {
	function getDanmuConf(){
		$danmu_config_m=new M('danmu_config');
		$danmu_config=$danmu_config_m->find();
		unset($danmu_config['id']);
		if($danmu_config['looptime']<=0){
			$danmu_config['looptime']=3;
		}
		return $danmu_config;
	}
}
