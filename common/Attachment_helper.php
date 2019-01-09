<?php
//根据id获取附件信息
if(!function_exists('GetAttachmentById')){
	function GetAttachmentById($id){
		$attachments_m=new M('attachments');
		$attachmentinfo=$attachments_m->find('id='.$id);
		return $attachmentinfo;
	}
}
