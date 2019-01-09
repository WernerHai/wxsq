<?php
require_once ('../data/config.php');
require_once ('../common/File_helper.php');
class FileUploadFactory {
	// 参数有file,aliyunoss,sae
	var $fileuploader;
	var $_type=null;
	function __construct($type = 'file') {
		switch ($type) {
			case 'aliyunoss' :
				$this->_type=2;
				require_once ('AliyunossFileUpoad.php');
				$this->fileuploader = new AliyunossFileUpoad ();
				// code...
				break;
			case 'sae' :
				$this->_type=3;
				$this->fileuploader = new SAEFileUpload ();
				break;
			default :
				$this->_type=1;
				require_once ('FileUpload.php');
				$this->fileuploader = new FileUpload ();
				break;
		}
	}
	function SaveRemotePic($picurl, $webroot) {
		return $this->fileuploader->SaveRemotePic ( $picurl, $webroot );
	}
	//保存表单中的文件
	function SaveFormFile($formfiledata, $filename = '', $path='') {
		$filepath=$this->fileuploader->SaveFormFile ( $formfiledata, $filename, $path );
		$extension=GetFileExtension($formfiledata['name']);
		$attachmentinfo=$this->saveAttachment($filepath,$extension);
		return $attachmentinfo;
	}
	//保存文件
	function SaveFile($filecontent, $extension, $webroot) {
		$filepath=$this->fileuploader->SaveFile ( $filecontent, $extension, $webroot );
		return $this->saveAttachment($filepath,$extension);
	}
	
	private function saveAttachment($filepath,$extension){
		$attachments_m=new M('attachments');
		$attachement_data=array(
				'filepath'=>$filepath,
				'extension'=>$extension,
				'type'=>$this->_type
		);
		$attachment_id=$attachments_m->add($attachement_data);
		$attachement_data['id']=$attachment_id;
		return $attachement_data;
	}
}