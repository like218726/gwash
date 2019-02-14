<?php
namespace app\admin\model;

use think\Db;

use think\Cache;

use think\Model;

class SystemWelcome extends Model {
	
	/**
	 * 
	 * windows系统下项目基础信息
	 * 
	 */
	public function get_windows_sys_info() {
		$sys_info['os']             	   = PHP_OS;
		$sys_info['zlib']           	   = function_exists('gzclose') ? 'YES' : 'NO';//zlib
		$sys_info['safe_mode']      	   = (boolean) ini_get('safe_mode') ? 'YES' : 'NO';//safe_mode = Off		
		$sys_info['timezone']       	   = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
		$sys_info['curl']				   = function_exists('curl_init') ? 'YES' : 'NO';	
		$sys_info['web_server']     	   = $_SERVER['SERVER_SOFTWARE'];
		$sys_info['phpv']           	   = phpversion();
		$sys_info['ip'] 				   = GetHostByName($_SERVER['SERVER_NAME']);
		$sys_info['fileupload']     	   = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
		$sys_info['max_ex_time'] 		   = @ini_get("max_execution_time").'s'; //脚本最大执行时间
		$sys_info['set_time_limit'] 	   = function_exists("set_time_limit") ? true : false;
		$sys_info['domain'] 			   = $_SERVER['HTTP_HOST'];
		$sys_info['memory_limit']   	   = get_cfg_var ("memory_limit")?get_cfg_var("memory_limit"):"无";		
        $sys_info['version']   	    	   = config('APP_VERSION');
		$mysqlinfo = db()->query("SELECT VERSION() as version");
		$sys_info['mysql_version']  	   = $mysqlinfo['0']['version'];
		if(function_exists("gd_info")){
			$gd = gd_info();
			$sys_info['gdinfo'] 		   = $gd['GD Version'];
		}else {
			$sys_info['gdinfo'] 		   = "未知";
		}
		$sys_info['allow_persistent'] 	   = @get_cfg_var("mysql.allow_persistent")?"是 ":"否";
		$sys_info['max_links'] 			   = @get_cfg_var("mysql.max_links")==-1 ? "不限" : @get_cfg_var("mysql.max_links");
		$sys_info['zend_version'] 		   = zend_version();
		$sys_info['server_port']		   = $_SERVER["SERVER_PORT"];
		$sys_info['user_agent']			   = $_SERVER['HTTP_USER_AGENT'];
		$sys_info['doucment_root']		   = $_SERVER['DOCUMENT_ROOT'];
		$sys_info['protocol']			   = $_SERVER['SERVER_PROTOCOL'];
		$sys_info['request_method']		   = $_SERVER['REQUEST_METHOD'];
		$sys_info['server_time']		   = date("Y年n月j日 H:i:s");
		$sys_info['beijing_time']		   = gmdate("Y年n月j日 H:i:s",time()+8*3600);
		$sys_info['remote_addr']		   = $_SERVER['REMOTE_ADDR'];
		$sys_info['systemRoot']			   = $_SERVER['SystemRoot'];
		$sys_info['language']			   = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$sys_info['server_software']	   = $_SERVER['GATEWAY_INTERFACE'];
		if (is_file(ROOT_PATH.'README.md')) {
			$sys_info['thinkphp_version']  = file(ROOT_PATH.'README.md')['0'];
		} else {
			$sys_info['thinkphp_version']  = '';
		}
		Cache::set('sys_info', $sys_info);
		return Cache::has('sys_info') ? Cache::get('sys_info',$sys_info) : $sys_info;    		
	}
	
	/**
	 * 
	 * linux下项目基础信息
	 * 
	 */
	public function get_linux_sys_info() {
        $version = Db::query('SELECT VERSION() AS ver');
        $config  = [
            'url'             => $_SERVER['HTTP_HOST'],
            'document_root'   => $_SERVER['DOCUMENT_ROOT'],
            'server_os'       => PHP_OS,
            'server_port'     => $_SERVER['SERVER_PORT'],
            'server_soft'     => $_SERVER['SERVER_SOFTWARE'],
            'php_version'     => PHP_VERSION,
            'mysql_version'   => $version[0]['ver'],
            'max_upload_size' => ini_get('upload_max_filesize')
        ];
		if (is_file(ROOT_PATH.'README.md')) {
			$config['thinkphp_version']  = file(ROOT_PATH.'README.md')['0'];
		} else {
			$config['thinkphp_version']  = '';
		}       
		Cache::set('sys_info', $config);
		return Cache::has('sys_info') ? Cache::get('sys_info',$config) : $config;    			 		
	}
}