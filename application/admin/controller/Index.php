<?php
namespace app\admin\controller;

use think\Cache;

class Index extends Base { 
    public function index() {
    	if ($this->request->isGet()) {  
	        $isAdmin = isAdministrator(); 
	        $list = array();
	        $menuAll = $this->allMenu; 
	        foreach ($menuAll as $menu) {
	            if ($menu['status'] == 0) {
	                if ($isAdmin) { 
	                    $menu['url'] = url($menu['url']);
	                    $list[] = $menu;  
	                } else {
	                    $authObj = new Auth(); 
	                    $authList = $authObj->getAuthList($this->uid); 
	                    if (in_array(strtolower($menu['url']), $authList) || $menu['url'] == '') {
	                        $menu['url'] = url($menu['url']);
	                        $list[] = $menu;
	                    }
	                }
	            }
	        } ;
	        $list = listToTree($list); 
	       
	        foreach ($list as $key => $item) {
	            if(empty($item['_child']) && $item['url'] != url('Index/welcome')){
	                unset($list[$key]);
	            }
	        }
	        $list = formatTree($list);
	        return $this->fetch("index/index",["list"=>$list]);    		
    	}
    }
    
    /**
     * 
     * 欢迎页面
     * 
     */
    public function welcome(){
    	if ($this->request->isGet()) {
			$sys_info = $this->get_sys_info();	
			Cache::set('welcome','欢迎使用<b>'.config('APP_NAME').'</b>管理系统');
			Cache::set('sys_info',$sys_info);
			Cache::has('welcome') ? $welcome = cache('welcome') : $welcome = '欢迎使用<b>'.config('APP_NAME').'</b>管理系统';
			$this->assign('welcome',$welcome);	
			$this->assign('sys_info',$sys_info);
    		if (PHP_OS == 'WINNT') {
				$this->windowSsysInfo();
			} else {
				$this->linux_sys_info();
			}	
			$this->assign('os',PHP_OS);		
	    	return $this->fetch();    		
    	}
    }
    
    /**
     * 
     * 系统参数
     * 
     */
    public function get_sys_info(){
    	if ($this->request->isGet()) {
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
    } 

    /**
     * 
     * 授权信息
     * 
     */
    public function ShowAuth() {
    	if ($this->request->isGet()) {  
    		$auth1['0'] = ['name'=>'授权公司名称','value'=>'深圳市吉图软件开发有限公司'];
    		$auth1['1'] = ['name'=>'授权注册用户数','value'=>'1000'];
    		$auth1['2'] = ['name'=>'授权有效期','value'=>'2021-12-24'];    		
    		$copyright1 = str_replace('year', date('Y'), config('COPYRIGHT'));
    		$copyright1 = str_replace('company', config('COMPANY_NAME'), $copyright1);
    		Cache::set('auth',$auth1);
    		Cache::set('copyright',$copyright1);
    		Cache::has('auth') ? $auth = Cache::get('auth') : $auth;
    		Cache::has('copyright') ? Cache::get('copyright') : $copyright = $copyright1;
    		$this->assign('auth',$auth);
    		$this->assign('copyright',$copyright);
    		return $this->fetch('ShowAuth');
    	}
    }  

    /**
     * 
     * 版本信息
     * 
     */
    public function version() {
    	if ($this->request->isGet()) {  
    		$auth1['0'] = ['name'=>'当前系统','value'=>config('APP_NAME').'管理系统'];
    		$auth1['1'] = ['name'=>'版本号','value'=>'V 1.0'];
    		$auth1['2'] = ['name'=>'发布次数','value'=>'1'];
    		$auth1['3'] = ['name'=>'版本信息','value'=>'1.0'];
    		$auth1['4'] = ['name'=>'发布者','value'=>'JT'];
    		$auth1['5'] = ['name'=>'发布时间','value'=>'2018-12-24'];    		
    		$copyright1 = str_replace('year', date('Y'), config('COPYRIGHT'));
    		$copyright1 = str_replace('company', config('COMPANY_NAME'), $copyright1);
    		Cache::set('auth', $auth1);
    		Cache::set('copyright', $copyright1);
    		Cache::has('auth') ? $auth = Cache::get('auth') : $auth = $auth1;
    		Cache::has('copyright') ? $copyright = Cache::get('copyright') : $copyright = $copyright1;
    		$this->assign('auth',$auth);
    		$this->assign('copyright',$copyright);
    		return $this->fetch();
    	}
    } 

    /**
     * 
     * Windows系统信息
     * 
     */
    public function windowSsysInfo() {
    	set_time_limit(0);
    	ini_set('memory_limit', '1024M');
		Cache::set('windowssysinfo', windows_sysytem_info());
		Cache::has('windowssysinfo') ? $result=Cache::get('windowssysinfo') : $result = windows_sysytem_info();
		$this->assign('windowssysinfo',$result);
    	$this->fetch('windowSsysInfo');
    }
    
    /**
     * 
     * linux系统信息
     * 
     */
    public function linux_sys_info() {
    	set_time_limit(0);
    	ini_set('memory_limit', '1024M');
		Cache::set('linuxSysInfo', linux_system_info());
		Cache::has('linuxSysInfo') ? $result=Cache::get('linuxSysInfo') : $result = linux_system_info();
		$this->assign('linuxSysInfo',$result);
    	$this->fetch('linuxSysInfo');    	
    }
}