<?php
@header("Content-type: text/html; charset=utf-8");
require_once('../smarty/Smarty.class.php');
require_once(dirname(__FILE__) . '/../common/db.class.php');
require_once(dirname(__FILE__) . '/../common/session_helper.php');
require_once(dirname(__FILE__) . '/../common/function.php');
require_once(dirname(__FILE__) . '/../common/Attachment_helper.php');
class Page{
// 	var $title;
	var $_wall_config;
	var $_admin;
	var $_smarty;
	function __construct(){
		$this->_checkprivilege();
		$admin_m=new M('admin');
		//$wall_config_m=new M('wall_config');
		$this->_admin=$admin_m->find('1 limit 1');
		$this->_wall_config=getWallConf();
		
		$this->_smarty=new Smarty;
		$this->_smarty->caching = false;
		$apppath=str_replace(DIRECTORY_SEPARATOR.'myadmin', '', dirname(__FILE__));
		$this->_smarty->compile_dir = $apppath.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'templates_c'.DIRECTORY_SEPARATOR;
		$webpath=$_SERVER['HTTP_HOST'];
		$this->assign('admin',$this->_admin);
		$this->assign('wall_config',$this->_wall_config);
		$this->assign('domain',$webpath);
	}
	function setTitle($title){
		$this->assign('title',$title);
	}

	private function _checkprivilege(){
		if (!isset($_SESSION['admin']) || $_SESSION['admin'] != true) {
			$_SESSION['admin'] = false;
			echo "<script>window.location='login.php';</script>";
			exit();
		}
	}
	public function assign($varname,$varval){
		$this->_smarty->assign($varname,$varval);
	}
	public function display($path){
		$this->_smarty->display($path);
	}
	public function show(){
		
	}
	public function pagerhtml($page,$pagesize,$rowcount,$url){
		if($rowcount==0){
			return '';
		}
		$url=strpos($url, '?')===false?$url.'?page=':$url.'&page=';
		$html='<div class="widget-toolbox  clearfix"><ul class="pagination" style="margin:10px 0px;">';
		$pagenum=ceil($rowcount/$pagesize);
		$pagehtml='';
		for($i=1;$i<=$pagenum;$i++){
			$class=$i==$page?'class="active"':'';
			$pagehtml.='<li '.$class.'><a href="'.$url.$i.'">'.$i.'</a></li>';
		}
		$firstpagehtml='';
		if($page==1){
			$firstpagehtml='<li class="disabled"><a href="#"><i class="ace-icon fa fa-angle-double-left"></i></a></li>';
		}else{
			$firstpagehtml='<li ><a href="'.$url.'1"><i class="ace-icon fa fa-angle-double-left"></i></a></li>';
		}
		$lastpagehtml='';
		if($page==$pagenum){
			$lastpagehtml='<li class="disabled"><a href="#"><i class="ace-icon fa fa-angle-double-right"></i></a></li>';
		}else{
			$lastpagehtml='<li ><a href="'.$url.$pagenum.'"><i class="ace-icon fa fa-angle-double-right"></i></a></li>';
		}
		$html.=$firstpagehtml.$pagehtml.$lastpagehtml;
		$html.='<li><a href="###">共 '.$rowcount.'条数据</a></li></ul></div>';
		return $html;
	}
}