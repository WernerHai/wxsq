<?php
if(!function_exists('request_scheme')){
	function request_scheme(){
		$server_request_scheme='http';
		if ( (! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') || (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ) {
			$server_request_scheme = 'https';
		}
		return $server_request_scheme;
	}
}



