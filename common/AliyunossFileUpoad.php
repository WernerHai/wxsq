<?php
class AliyunossFileUpoad {
	function SaveRemotePic($picurl = '', $domain = '') {
		$extension = $this->getFileExtension ( $picurl );
		$imagedata = $this->getRemoteImageData ( $picurl );
		$returnfileurl = $this->SaveFile ( $imagedata, $extension, $domain );
		return $returnfileurl;
	}
	public function SaveFormFile($formfiledata, $filename = '', $path) {
		$tp = array (
				"image/pjpeg",
				"image/jpeg",
				"image/png" 
		);
		// 检查上传文件是否在允许上传的类型
		if (! in_array ( $formfiledata ["type"], $tp )) {
			return false;
			// echo "不能上传该文件格式";
			// exit;
		}
		$extension = $this->getFileExtension ( $formfiledata ['name'] );
		
		$basepath = str_replace ( DIRECTORY_SEPARATOR . 'common', DIRECTORY_SEPARATOR, dirname ( __FILE__ ) );
		require_once ($basepath . 'library/aliyunosssdk/sdk.class.php');
		$oss_sdk_service = new ALIOSS ();
		// $bucket = BUCKET_NAME;
		$object = "pic_" . time () . '.' . $extension;
		$systempath=str_replace('/common/AliyunossFileUpoad.php', '/', __FILE__);
// 		$tempfile=$systempath.'data'.DIRECTORY_SEPARATOR.'pic'.DIRECTORY_SEPARATOR.$object;
// 		echo $formfiledata ['tmp_name'];
// 		exit();
// 		echo $tempfile;
// 		echo __FILE__;
// 		/var/www/com.wxmiao.xc/common/AliyunossFileUpoad.php
		
// 		exit();
// 		move_uploaded_file($formfiledata ['tmp_name'],$tempfile);
		
		$response_upload_file_by_file = $oss_sdk_service->upload_file_by_file ( BUCKET_NAME, $object, $formfiledata ['tmp_name']);
		$weburl = 'http://' . BUCKET_NAME . '.oss-cn-hangzhou.aliyuncs.com/' . $object;
		return $weburl;
	}
	function getRemoteImageData($picurl) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $picurl );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false ); // 对认证证书来源的检查
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false ); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1521.3 Safari/537.36" );
		$output = curl_exec ( $ch );
		curl_close ( $ch );
		return $output;
		// return array('data'=>$output,'extension'=>)
	}
	function getFileExtension($picurl) {
		$pathinfo = pathinfo ( $picurl );
		$extension = isset ( $pathinfo ['extension'] ) ? $pathinfo ['extension'] : '';
		$extension = strtolower ( $extension );
		switch ($extension) {
			case 'jpg' :
				// code...
				break;
			case 'png' :
				break;
			case 'gif' :
				break;
			default :
				$extension = 'jpg';
				// code...
				break;
		}
		return $extension;
	}
	function SaveFile($output, $extension, $domain) {
		$basepath = str_replace ( DIRECTORY_SEPARATOR . 'common', DIRECTORY_SEPARATOR, dirname ( __FILE__ ) );
		require_once ($basepath . 'library/aliyunosssdk/sdk.class.php');
		$oss_sdk_service = new ALIOSS ();
		$bucket = BUCKET_NAME;
		$object = "pic_" . time ().uniqid(). '.' . $extension;
		$file_content_array = array (
				'content' => $output,
				'length' => strlen ( $output ),
				'headers' => array (
						'Cache-control' => 'max-age=864000' 
				) 
		);
		$response_upload_file_by_file = $oss_sdk_service->upload_file_by_content ( $bucket, $object, $file_content_array );
		$weburl = 'http://' . BUCKET_NAME . '.oss-cn-hangzhou.aliyuncs.com/' . $object;
		return $weburl;
	}
}
