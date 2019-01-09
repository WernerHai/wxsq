<?php
require_once('File_helper.php');
class FileUpload{
        var $savepath;
        //保存远程图片到本地服务器
        function __construct() {
            $basepath=str_replace(DIRECTORY_SEPARATOR.'common', DIRECTORY_SEPARATOR, dirname(__FILE__));
            $this->savepath=$basepath.'data'.DIRECTORY_SEPARATOR.'pic'.DIRECTORY_SEPARATOR;
        }
	function SaveRemotePic($picurl='',$domain=''){
		$extension=GetFileExtension($picurl);//$this->getFileExtension($picurl);
		$imagedata=$this->getRemoteImageData($picurl);
		$returnfileurl=$this->SaveFile($imagedata,$extension,$domain);
		return $returnfileurl;
	}
        
        function SaveFormFile($formfiledata,$filename='',$path){
            $tp = array("image/pjpeg", "image/jpeg", "image/png");
            //检查上传文件是否在允许上传的类型
            if (!in_array($formfiledata["type"], $tp)) {
                return false;
            }
            $extension=GetFileExtension($formfiledata['name']);//$this->getFileExtension($formfiledata['name']);
            if($this->local_mkdirs($this->savepath)){
                $destnationfilename="pic_" . time() .'.'.$extension;
                $destnation=$this->savepath.$destnationfilename;
                $result=move_uploaded_file($formfiledata['tmp_name'], $destnation);
                return "/data/pic/".$destnationfilename;
            }
            return false;
        }
	function getRemoteImageData($picurl){
            $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $picurl);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1521.3 Safari/537.36");
	    $output = curl_exec($ch);
	    curl_close($ch);
	    return $output;
	    // return array('data'=>$output,'extension'=>)
	}

// 	function getFileExtension($picurl){
// 		$pathinfo=pathinfo($picurl);
// 		$extension=isset($pathinfo['extension'])?$pathinfo['extension']:'';
// 		$extension=strtolower($extension);
// 		switch ($extension) {
// 			case 'jpg':
// 				# code...
// 				break;
// 			case 'png':
// 				break;
// 			case 'gif':
// 				break;
// 			default:
// 				$extension='jpg';
// 				# code...
// 				break;
// 		}
// 		return $extension;
// 	}
	
	function local_mkdirs($path)
	{
	    if (!is_dir($path)) {
	        $this->local_mkdirs(dirname($path));
	        mkdir($path);
	    }
	    return is_dir($path);
	}


	function SaveFile($write,$extension, $webpath)
	{   
	    if($this->local_mkdirs($this->savepath)){
	        $filename = "pic_" . time().rand(10000,99999) .'.'.$extension;
	        $file = fopen($this->savepath . $filename, "w");
	        fwrite($file, $write);//写入
	        fclose($file);//关闭
	        $imgurl = $webpath . "/data/pic/" . $filename;
	        return $imgurl;
	    }
	    return false;    
	}
}