<?php

namespace app\admin\controller;

use think\Cache;

class Index extends Base { 
	
	public function _initialize() {
		parent::_initialize();
	}
	
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
	        $this->assign('real_name',session('real_name'));
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
    		$sys_info = '';
    		$welcome = '';
    		$teplate_welcome = '';
    		if (strtoupper(PHP_OS) == 'WINNT') {
    			$sys_info = model('SystemWelcome')->get_windows_sys_info();	
    			Cache::has('welcome') ? $welcome = cache('welcome') : $welcome = '欢迎使用<b>'.config('APP_NAME').'</b>管理系统';
    			$teplate_welcome = 'windows_welcome';
    			$this->windowSsysInfo();
    		} else {
     			$sys_info = $this->get_linux_sys_info();	
    			Cache::has('welcome') ? $welcome = cache('welcome') : $welcome = '欢迎使用<b>'.config('APP_NAME').'</b>管理系统';   
    			$teplate_welcome = 'linux_welcome';			
    		}
 			$this->assign('welcome',$welcome);	
			$this->assign('sys_info',$sys_info);   		
    		return $this->fetch($teplate_welcome);   		
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
    		Cache::has('copyright') ? $copyright = Cache::get('copyright') : $copyright = $copyright1;
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
    		$app_version = config('APP_VERSION');
    		$app_version_arr = explode('.', substr($app_version, 1)); 
    		if ($app_version_arr['0'] != 0 && $app_version_arr['1'] == 0 && $app_version_arr['2'] == 0) { 
    			$version_num = $app_version_arr['0'].'00';
    		} elseif ($app_version_arr['0'] != 0 && $app_version_arr['1'] == 0 && $app_version_arr['2'] != 0) { 
    			$version_num = $app_version_arr['0'].'00' + $app_version_arr['2'];
    		} elseif($app_version_arr['0'] != 0 && $app_version_arr['1'] != 0) { 
    			$version_num = $app_version_arr['0'].'00' + ($app_version_arr['1'].$app_version_arr['2']);
    		} elseif ($app_version_arr['0'] == 0 && $app_version_arr['1'] == 0 && $app_version_arr['2'] != 0) {
    			$version_num = $app_version_arr['2'];
    		} elseif ($app_version_arr['0'] == 0 && $app_version_arr['1'] > 0) { 
    			$version_num = $app_version_arr['1'].$app_version_arr['2'];
    		} elseif ($app_version_arr['0'] == 0 && $app_version_arr['1'] == 0) { 
    			$version_num = $app_version_arr['2'] + 1;
    		} else { 
    			$version_num = 0;
    		}
    		$version['0'] = ['name'=>'当前系统','value'=>config('APP_NAME').'管理系统'];
    		$version['1'] = ['name'=>'版本号','value'=>$app_version];
    		$version['2'] = ['name'=>'发布次数','value'=>$version_num];
    		$version['3'] = ['name'=>'版本信息','value'=>substr($app_version, 1)];
    		$version['4'] = ['name'=>'发布者','value'=>config('COMPANY_NAME')];
    		$version['5'] = ['name'=>'发布时间','value'=>config('VERSION_TIME')];    		
    		Cache::set('version', $version);
    		Cache::has('version') ? $version = Cache::get('version') : $version;
    		$this->assign('version',$version);
    		$this->assign('copyright',Cache::get('copyright'));
    		return $this->fetch();
    	}
    } 

    /**
     * 
     * Windows系统信息
     * 
     */
    public function windowSsysInfo() {
    	if ($this->request->isGet()) {
	    	set_time_limit(0);
	    	ini_set('memory_limit', '1024M');
			Cache::set('windowssysinfo', windows_sysytem_info());
			Cache::has('windowssysinfo') ? $result=Cache::get('windowssysinfo') : $result = windows_sysytem_info();
			$this->assign('windowssysinfo',$result); 
	    	$this->fetch('windowSsysInfo');    		
    	}
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