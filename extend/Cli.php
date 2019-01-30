<?php
use think\Loader;
class Cli{
	private $php_cli_log_path;
	private $php_cli_task_info_cache;
	private $cache_obj;
	private $set_last_run_time_cfg;
	private $run_idx = 0;
	private $task_params = array();
	private $has_set_last_run_time = 0;
	private $task_stop_tag = array();
	private $last_time_check_bgtask_is_end;
	
	function __construct(){
			$sys_cache_path = get_sys_cache_path();
			$_path1 = $sys_cache_path.'/Tmp/';
			$this->php_cli_log_path = $_path1.'php_cli_log/';
			
			$this->php_cli_task_info_cache = $sys_cache_path.'/Tmp/cli_oms_task_info_cache';
			Loader::import('Cli', EXTEND_PATH, '.php'); 
			Loader::import('Secache', EXTEND_PATH, '.php'); 
			//$secache= new \Secache();
//			$this->cache_obj = MC('Secache','Lib/Core',null,'_g_php_cli_task_info_cache'); //这里要修改
			$this->cache_obj = new Secache();
			$this->cache_obj->workat($this->php_cli_task_info_cache);

			if (!file_exists($this->php_cli_log_path)){
				mkdir($this->php_cli_log_path,0777,true);
			}
	}

	//设置前台显示后台任务运行情况的最后一次请求时间 last_page_request_time
	function set_last_page_request_time($task_id,$time = null){
		$time = empty($time) ? time() : $time;
		$ckey = md5('last_page_request_time_'.$task_id);
		$this->cache_obj->store($ckey,$time);
		//error_log('set_last_page_request_time='.$ckey.'='.$time."\n",3,'D:\work\fluxoms\Cache\xx.xx');
	}
	
	//后台任务定时验证前台跑后台任务的最后一次活动时间,如果超时，后台任务自动退出
	function check_bgtask_is_end($task_id,$time_out = 10){
		if (time()-$this->last_time_check_bgtask_is_end<2){
			return;
		}
		$ckey = md5('last_page_request_time_'.$task_id);
		$this->cache_obj->fetch($ckey,$time);
		//error_log($task_id.'='.time().'='.$ckey.'='.$time."\n",3,'D:\work\fluxoms\Cache\xx.xx');
		$this->last_time_check_bgtask_is_end = time();
		//return;
		if (time()-$time>$time_out && !empty($time)){
			$_task_end_info = array('act'=>'show','value'=>__('页面已关闭，任务中断'));
			echo "<##php_cli_task_end##>".json_encode($_task_end_info)."</##php_cli_task_end##>";
			die;
		}
	}
	
	
	//方法作废
	function init_auto_check_set_last_run_time($set_last_run_time_cfg,$task_params){
		/*
		return;
		$this->set_last_run_time_cfg = explode(',',$set_last_run_time_cfg);
		$this->run_idx = 0;
		$this->task_params = $task_params;*/
	}
	
	//方法作废
	function auto_check_set_last_run_time($flag = 0){
		/*
		if (!isset($this->task_params['_g_run_task_id'])){
			return;
		}
		$task_id = $this->task_params['_g_run_task_id'];
		$run_mode = $this->task_params['_g_run_mode'];
		if ($flag == 1 && $this->has_set_last_run_time == 0){
			$this->check_set_last_run_time($task_id,$run_mode);
			return $this->check_task_stop_tag();
		}				
		if ($run_mode == 'web'){
			$cur_idx_cfg = $this->set_last_run_time_cfg[0];
		}else{
			$cur_idx_cfg = $this->set_last_run_time_cfg[1];			
		}
		$this->run_idx++;
		if ($this->run_idx % $cur_idx_cfg == 0){
			$this->check_set_last_run_time($task_id,$run_mode);
			$this->has_set_last_run_time = 1;
		}
		return $this->check_task_stop_tag();*/
	}

	/*
	方法作废--检查任务中断标识(返回true表示程序要中断)
	*/
	function check_task_stop_tag(){
		/*
		if (!isset($this->task_stop_tag['status'])){
			return false;
		}
		if ($this->task_stop_tag['status']>0){
			return false;
		}
		$_task_end_info = array('act'=>'show','value'=>$this->task_stop_tag['msg']);
		echo "<##php_cli_task_end##>".json_encode($_task_end_info)."</##php_cli_task_end##>";
		die;*/
	}
	
	//方法作废--设置任务最后一次的活动时间 并 检查后台任务last_page_request_time,超出4秒，任务自动中断 
	function check_set_last_run_time($task_id,$run_mode = 'web',$time = null,$timeout = 6){
		/*
		$this->set_last_run_time($task_id,$time);
		//check_last_page_request_time 只有在web方式下跑，check_stop_task_from_web 是web和cli方式下都可能跑到
		$msg = '';
		if ($run_mode == 'web'){
			$ret = $this->check_last_page_request_time($task_id,$timeout);
			if ($ret === true){
				$msg = "页面已关闭，任务中断";
			}			
		}else{
			$ret = false;
		}
		if ($ret === false){
			$ret = $this->check_stop_task_from_web($task_id);
			if ($ret === true){
				$msg = "发现任务结束标识，任务中断";
			}			
		}
		if ($ret === true){
			$msg = '任务已完成';
			$this->task_stop_tag = array('status'=>-1,'msg'=>$msg);
		}else{
			$this->task_stop_tag = array('status'=>1,'msg'=>'');
		}
		return $this->task_stop_tag;*/
	}
	
	//方法作废--设置任务最后一次的活动时间
	function set_last_run_time($task_id,$time = null){
		/*
		$time = empty($time) ? time() : $time;
		$ckey = md5('last_run_time_'.$task_id);
		$this->cache_obj->store($ckey,$time);*/
	}


	//后台任务检查 last_page_request_time,超出4秒，任务自动中断(返回true)
	function check_last_page_request_time($task_id,$timeout = 6){
		/*
		$ckey = md5('last_page_request_time_'.$task_id);
		$this->cache_obj->fetch($ckey,$val);
		//echo '<hr/>xxxxxxxxx<xmp>'.var_export($val,true).'</xmp>';die;
		$sub_val = time() - $val;
		if ((int)$sub_val>(int)$timeout){
			return true;
		}else{
			return false;
		}*/
	}

	//方法作废--从前台设置，任务结束的标识
	function set_stop_task_from_web($task_id){
		/*
		$ckey = md5('stop_task_'.$task_id);
		$this->cache_obj->store($ckey,1);*/	
	}

	//方法作废--验证是否从前台设置了任务结束的标识(如果设置返回true)--用于实现页面跑定时器的暂停功能
	function check_stop_task_from_web($task_id){
		/*
		$ckey = md5('stop_task_'.$task_id);
		$this->cache_obj->fetch($ckey,$val);
		$ret = empty($val) ? false : true;
		return $ret;*/		
	}
	
	function realPath()
	{
		if(substr(strtolower(PHP_OS), 0, 3) == 'win'){
			//ob_clean();
			ob_start();
			phpinfo(INFO_GENERAL);
			$s_phpinfostr = ob_get_contents();		
			ob_end_clean();
			$phpinfostr = str_replace(array(chr(10),chr(13)),'',$s_phpinfostr);
			$path = '';
			if (strpos($phpinfostr,'</td><td class="v">CGI/FastCGI')!== false){
				preg_match_all("/([^>]+)php\.ini/",$phpinfostr,$mats);
				$path = $mats[1][1];
			}
			
			if (empty($path)){
				$phpinfostr2 = str_replace(array(chr(10),chr(13)),' ',$s_phpinfostr);
				preg_match_all("/([^>]+)php\.ini/",$phpinfostr2,$mats);
				$path = $mats[1][1];			
			}
			
			if (empty($path)){
				$ini= ini_get_all();
				$path = $ini['extension_dir']['local_value'];
			}
			
			$php_path = str_replace('\\','/',$path);
			$php_path = str_replace(array('/ext/','/ext'),array('/','/'),$php_path);
			$real_path = $php_path.'php.exe';
		}else{
			$real_path = PHP_BINDIR.'/php';
		}
		//echo $real_path;die;
		if(strpos($real_path, 'ephp.exe') !== FALSE)
		{
			$real_path = str_replace('ephp.exe', 'php.exe',$real_path);
		}

		return $real_path;
	}

	function linuxAutoKillRun($cmd){
		$handle = popen("ps -ef | grep php 2>&1", 'r');
		$cmd_cont = '';
		while(!feof($handle)) {
			$buffer = fgets($handle);
			$cmd_cont .= $buffer;
		}
		pclose($handle);
		$t_arr_cmd = explode("\n",$cmd_cont);
		$find_cmd = "fluxoms_".md5($cmd);
		foreach($t_arr_cmd as $sub_str){
			if (strpos($sub_str,$find_cmd) !== false){
				preg_match("/^[^ ]+ +(\d+)/i",$sub_str,$mats);
				$task_pid = (int)$mats[1];
				if($task_pid >0){
					$handle = popen("kill $task_pid", 'r');
					if($handle === false){
						$ret = false;
					}else{
						$ret = true;
					}
					pclose($handle);
					return $ret;
				}
			}
		}
		return true;
	}

	function winAutoKillRun($cmd){
		$handle = popen("tasklist -v -fo csv", 'r');
		$cmd_cont = '';
		while(!feof($handle)) {
			$buffer = fgets($handle);
			$cmd_cont .= $buffer;
		}
		pclose($handle);

		$find_cmd = "fluxoms_".md5($cmd);
		$t_arr_cmd = explode("\n",$cmd_cont);
		foreach($t_arr_cmd as $sub_str){
			$t_a = str_getcsv($sub_str);
			if (!isset($t_a[1])){
				continue;
			}
			$cur_task_pid = $t_a[1];
			$cur_task_name = $t_a[8];
			if (strpos($cur_task_name,$find_cmd) !== false){
				$handle = popen("taskkill -pid {$cur_task_pid}", 'r');
				if($handle === false){
					$ret = false;
				}else{
					$ret = true;
				}
				pclose($handle);
				//echo '<hr/>$ret<xmp>'.var_export($ret,true).'</xmp>';
				return $ret;
			}
		}
		return true;
	}

	/**
	_g_run_mode = web | cli; -- 这个指定是页面跑还是后台定时器跑
	_g_run_task_id = 任务的ID号 -- 任务的ID号,每个任务都不一样,用于生成输出缓存的文件名
	_g_run_tag = 任务的标识,命令行MD5值 -- 对于业务处理内容一样的任务，这个值是一样的
	_g_run_start = 开始的时间 --这个不参与任务业务处理，只是为了能在命令行中，直接看到什么时间开始跑的
	_g_run_user_id = 页面跑时，是哪个用户跑的
	$run_params = run_mode(可为空 默认为web),run_tag(可为空),run_task_id(可为空),run_user_id(run_mode = web时不可为空)
	*/
	function run($exec_command,$run_params = array(),$append_params = array()){
		
		preg_match('/\.php ([\w,\/,_,-]+) ([\w,_,-]+)/',$exec_command,$mats);
		if (!empty($mats[1]) && !empty($mats[2])){
			$default_run_task_id = str_replace('/','_',$mats[1]).'_'.$mats[2].'_'.uniqid();			
		}else{
			$default_run_task_id = uniqid();
		}

		$g_params = array();
		$g_params['_g_run_mode'] = isset($run_params['run_mode']) ? $run_params['run_mode'] : 'web';
		$g_params['_g_run_tag'] = isset($run_params['run_tag']) ? $run_params['run_tag'] : md5($g_params['_g_run_mode'].$exec_command);
		$task_id = $g_params['_g_run_task_id'] = isset($run_params['run_task_id']) ? $run_params['run_task_id'] : $default_run_task_id;
		$g_params['_g_run_start'] = isset($run_params['run_start']) ? $run_params['run_start'] : date('Y-m-d H:i:s');
		$g_params['_g_run_user_id']	= isset($run_params['run_user_id']) ? $run_params['run_user_id'] : 0;
		$_g_log_file_id = isset($run_params['run_log_file_id']) ? $run_params['run_log_file_id'] : $task_id; 
		$log_file = $g_params['_g_log_file'] = $this->getLogFile($_g_log_file_id);		
		$g_params_2 = array();
		foreach($g_params as $k=>$v){
			$g_params_2[] = '--'.$k.'="'.$v.'"';
		}
		foreach($append_params as $k=>$v){
			$g_params_2[] = '--'.$k.'="'.$v.'"';
		}
		$_run_exec_command = $exec_command.' '.join(' ',$g_params_2);
		//return;
		//error_log($_run_exec_command."\n",3,APP_PATH.'Cache/cli.txt');die;
		//echo $_run_exec_command;die;
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
			$this->winRun($_run_exec_command,$log_file);
		}else{
			popen($_run_exec_command."  1>>{$log_file} 2>>{$log_file} &",'r');
		}
		return $task_id;
	}

	function winRun($cmd,$log_file){
		$_sys_cache_path = get_sys_cache_path();
		$bat_str = "title fluxoms_".md5($cmd)."\r\n".$cmd." >> {$log_file} \r\n exit";
		$bat_file_path =  $_sys_cache_path .'/Tmp/auto_task_bat/';
		if (!file_exists($bat_file_path)){
			mkdir($bat_file_path,0777,true);
		}
		$bat_file = $bat_file_path . md5($cmd) .'.bat';
		file_put_contents($bat_file,$bat_str);
		popen('start /min '. $bat_file,'r');
		sleep(1);
	}

	function getLogFile($task_id){
		$log_file = $this->php_cli_log_path . $task_id .'.log';
		return $log_file;
	}

	function getRunResult($task_id,$log_file_offset){
		$this->set_last_page_request_time($task_id);
		$log_file = $this->php_cli_log_path . $task_id .'.log';
		$handle = fopen($log_file, "r");
		if($handle === false){
			$ret = __('任务执行情况的缓存文件打开失败').$log_file;
		}else{
			if($log_file_offset>0){
				fseek($handle,$log_file_offset);
			}

			$buffer_length = 0;
			$buffer = '';
			while($buffer_length<=4096 && !feof($handle)){
				$buffer .= fgets($handle, 4096);
				$buffer_length = strlen($buffer);
			}

			if($buffer == '' || is_null($buffer)){
				fseek($handle,0,SEEK_END);
			}

			$log_file_offset = ftell($handle);

			$ret = $log_file_offset.'####'.$buffer;
		}
		return $ret;
	}

	function get_cmd_path(){
		$exec_command = $this->realPath() . ' -f ' . BASE_PATH;
		return $exec_command;
	}

	function linux_shell_run($cmd,$cd_path='',$err_redirect=1){
		if(substr(strtolower(PHP_OS), 0, 3) == 'win'){
			return array('status'=>-1,'msg'=>'linux_shell_run_err:command not support windows');
		}
		$err_tag = '@##@shell run err@##@';
		$err_redirect_flag = $err_redirect ? ' 2>&1 ' : '';
		$cmd = "{$cmd} {$err_redirect_flag} || echo '{$err_tag}'";
		if (!empty($cd_path)){
			$cmd = "cd {$cd_path} && ".$cmd;
		}
		$handle = popen($cmd,'r');
		//error_log("\n".$cmd."\n\n",3,'/data/Cache/cli.log');
		
		$cmd_cont = '';
		while(!feof($handle)) {
			$buffer = fgets($handle);
			$cmd_cont .= $buffer;
		}
		//echo $cmd;
		//echo '<hr/>cmd_contcmd_cont<xmp>'.var_export($cmd_cont,true).'</xmp>';die;
		
		//error_log("\n!!".$cmd_cont."\n\n",3,'/data/Cache/cli.log');
		$_ts = substr($cmd_cont,-25);
		if (strpos($_ts,$err_tag) !== false){
			//echo "\n".$cmd."\n";
			$err_msg = trim(str_replace($err_tag,'',$cmd_cont));
			return array('status'=>-1,'msg'=>'linux_shell_run_err:'.$err_msg);
		}
		pclose($handle);
		return array('status'=>1,'cmd_cont'=>$cmd_cont);
	}
	
	function awk_shell_run($cmd,$cd_path='',$err_file_name){
		$ret = $this->linux_shell_run($cmd,$cd_path,0);
		if ($ret['status']<0){
			return $ret;
		}
		$check_ret = $this->check_shell_err($err_file_name);
		if ($check_ret !== false){
			return array('status'=>-1,'msg'=>'awk_run_err:'.$check_ret);
		}
		return $ret;
	}
	
	function check_shell_err($err_file_name){
		if (!file_exists($err_file_name)){
			return false;
		}
		$str = file_get_contents($err_file_name);
		
		if (trim($str) == ''){
			@unlink($err_file_name);
			return false;
		}
		$arr = explode("\n",trim($str));
		$result = trim(str_replace(' ','',end($arr)));
		
		return $result;
	}
	
}
