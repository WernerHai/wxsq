<?php
class CacheFactory{
	var $mem;
	var $cachetype;
	function __construct($type='file'){
		$this->cachetype=$type;
		if($type=='file'){
			//文件式缓存
			require('filecache.class.php');
			$this->mem= new file_cache;
		}else{
			//默认使用阿里云的memcached 缓存
			require('memcached.class.php');
			//memcached缓存
			$this->mem= new memcached_cache;
		}
	}
	public function set($key,$val,$limittime){
		return $this->mem->set($key,$val,$limittime);
	}
	public function get($key){
		return $this->mem->get($key);
	}
	public function clear_all(){
		return $this->mem->clear_all();
	}
	public function quit(){
		if($this->cachetype!='file'){
			return $this->mem->quit();
		}
		return true;
	}
	public function delete($key){
		return $this->mem->delete($key);
	}
}