<?php
/**
 * 主定时器类
 * Enter description here ...
 * @author lyw
 * 主定时器： /usr/local/php/bin/php /var/www/html/family/index.php AutoTask Run
 *
 */

namespace app\admin\controller;

use think\Loader;

use think\Db;

class AutoTask extends Base{
	protected $run_log = 0; //是否记录自动运行日志

	/**
	 * 
	 * php  d:/xampps/php/php.exe e:/www/flux_oms/OMS/index.php AutoTask Run
	 * 主定时器运行
	 * Enter description here ...
	 */
	public function actionRun()
	{
		$configModel = $this->loadModel('Home/Config');
		$row = $configModel->where(array('code'=>'auto_task_is_on'))->getRow();
		if($row && $row['value']==0){
			exit("系统任务总开关已关闭");
		}
		$taskModel = $this->loadModel('Home/TaskModel');
		$taskModel->where(array('is_on'=>1));
		
		$where = array('is_on'=>1);
		
		$taskModel->where($where);
		$rows = $taskModel->getAll();
		$real_path = $this->realPath();
	    //$this->rootPath = BASE_PATH.FILE_NAME;
	    $this->rootPath = BASE_PATH;
	    $time = date('Y-m-d H:i:s');
	    $week = date("w");
	    $htime = date('H:i');
	    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
	    	$run_php_cmds = $this->getWinTaskList();
	    }else{
	    	$run_php_cmds = $this->executeCmd('ps -ef | grep php');
	    }
	    $logs = array();
		foreach($rows as $row)
		{
			//计划时间执行
			$is_plan_run = $this->is_plan_run($row);
			
			$last_exec_time = (int)$row['last_exec_time'];
			$task_name = $row['task_name'];
			$lx_time = (int)$row['lx_time'];
			$id = (int)$row['task_id'];
			$ip = $row['allow_ips'];
			$process_num = $row['process_num'];
			$shop_id = implode(',', array_filter(explode(',',$row['shop_ids'])));
			if (!$is_plan_run && $last_exec_time && ($last_exec_time >= ((time()+20) - $lx_time*60))){
				$log = date('Y-m-d H:i:s').'--'.$task_name.__('--启动失败--还未到下次轮询时间')."\n";
				$logs[] = $log;
				echo $log;
				//echo 'last_run_time==='.date('Y-m-d H:i:s', $last_exec_time);
				//echo '===('.date('Y-m-d H:i:s',time()).'-'.$lx_time.'*60)';
				//echo "\n";
				continue;
			}
			
			//判断是否到了计划执行的时间
			if($is_plan_run){
				if(!in_array(7, $row['run_days']) && !in_array($week, $row['run_days']) ){
					$log = date('Y-m-d H:i:s').'--'.$task_name.__('--启动失败--非指定执行时间')."\n";
					$logs[] = $log;
					echo $log;
					continue;
				}else{
					if(!in_array($htime, $row['run_times'])){
						$log = date('Y-m-d H:i:s').'--'.$task_name.__('--启动失败--非指定执行时间点')."\n";
						$logs[] = $log;
						echo $log;
						continue;
					}
				}
			}
			
		    $cmd = trim($row['cmd']);
			
			$exec_command = $real_path.' -f '.$this->rootPath.$cmd;
			
			//进程数
			if($process_num>0){
				$exec_command .=' --_g_run_child_task_num='.$process_num;
			}
			
			//店铺id
			if($shop_id){
				$exec_command .=' --shop_id='.$shop_id;
			}
		    
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
				//window
				if (!$this->win_chk_is_run($exec_command, $run_php_cmds)){
					if($this->ipCheck($ip)){
						$this->win_run($exec_command);
						$log = date('Y-m-d H:i:s').'--'.$task_name.'--'.$exec_command."\n";
						$logs[] = $log;
						echo $log;
						$taskModel->save(array('last_exec_time'=>time()), array('task_id'=>$id));
					}
				}else{
					echo "Is runing--".$exec_command."\n";
					continue ;
				}
			}else{
				//linux
				if (!$this->chk_is_run($exec_command, $run_php_cmds)){
					if($this->ipCheck($ip)){
						popen($exec_command.' >/dev/null 2>&1 &','r');
						$log = date('Y-m-d H:i:s').'--'.$task_name.__('--启动成功--').$exec_command."\n";
						$logs[] = $log;
						echo $log;
						$taskModel->save(array('last_exec_time'=>time()), array('task_id'=>$id));
					}
				}else{
					$log = date('Y-m-d H:i:s').'--'.$task_name.__('--本次启动失败--该任务已在运行--').$exec_command."\n"; 
					$logs[] = $log;
					echo $log;
					continue;
				}
			}
		}
		$this->writeAutoRunLog($logs);
		$this->auto_kill_timeout_task();		
	}
	
	function is_plan_run(&$row)
	{
		$row['run_times'] = json_decode($row['run_times'], true);
		$row['run_times'] = $row['run_times'] ? array_filter($row['run_times']) : array();
		$row['run_days'] = array_filter(explode(',',$row['run_days']));
		if($row['run_days'] && $row['run_times'])
			return true;
		return false;
	}
	
	//运行日志记录
	public function writeAutoRunLog($logs)
	{
		$log = array();
		if($this->run_log && $logs){
			$log[] = $content = implode('', $logs);
			$log_path = CACHE_PATH.'Tmp/AutoRunLog.log';
			if (!file_exists($log_path)){
				touch($log_path);
				chmod($log_path, 0777);
			}
			@file_put_contents($log_path,implode("\t",$log)."\n\n",FILE_APPEND | LOCK_EX);
		}
	}
	
	/**
	 * 执行一条任务 2019-01-12
	 * Enter description here ...
	 */
	public function RunOneCommand()
	{
	
		$params = input();

		$type = $params['type'];
		$command_script = trim($params['command_script']);
		$id = (int)$params['id'];
		
		$timestamp = (int)substr($params['timestamp'],0,-3);
		
		$current_timestamp = time();
		
		if ($params['timestamp'] && abs($current_timestamp - $timestamp) > 3*60) {
			exit("和服务器时间差不能超过3分钟");
		}

		$row = Db::name('system_auto_task')->where('task_id',$id)->find();
		
		if (!$row) {
			exit("不存在的系统自动服务");
		}
		
		
		//检查是否大于轮询时间
		$lx_time = (int)$row['lx_time'];  //分钟
		
		$last_exec_time = (int)$row['last_exec_time'];  //上次执行时间戳
		
		if (($current_timestamp - $last_exec_time) < $lx_time*60) {
			$temp = $lx_time*60 - ($current_timestamp - $last_exec_time);
			//exit("距离上次执行还不到轮询时间，请等 {$temp} 秒后再试");
		}
		
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'){
	    	$run_php_cmds = $this->getWinTaskList();
	    	if ($this->win_chk_is_run($command_script, $run_php_cmds)){
	    		exit('启动命令失败，已经存在相同的命令正在运行');
	    	}
	    }else{
	    	$run_php_cmds = $this->executeCmd('ps -ef | grep php');
		    if ($this->chk_is_run($command_script, $run_php_cmds)){
				exit('启动命令失败，已经存在相同的命令正在运行');
			}
	    }
		
		//更新执行时间
		Db::name('system_auto_task')->where('task_id',$id)->update(array('last_exec_time'=> $current_timestamp));
		Loader::import('Cli', EXTEND_PATH, '.php');  
		$cli= new \cli();
		$task_id = $cli->run($command_script);
		if ($task_id === false){
			echo "启动命令行失败";die;
		}
		$BaseUrl = $_SERVER['SERVER_PORT']<>80 ? $_SERVER['REQUEST_SCHEME'].":".$_SERVER['SERVER_PORT']."://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'] : $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];	
		
		$this->assign('task_id', $task_id);
		$this->assign('BaseUrl', $BaseUrl);
		return $this->fetch('timer/RunOneCmdScript');
	}
	
	
	function realPath()
	{
		if(substr(strtolower(PHP_OS), 0, 3) == 'win'){
			ob_clean();
			ob_start();
			phpinfo(INFO_GENERAL);
			$phpinfostr = ob_get_contents();		
			ob_end_clean();
			$phpinfostr = str_replace(array(chr(10),chr(13)),'',$phpinfostr);
			if (strpos($phpinfostr,'</td><td class="v">CGI/FastCGI')!== false){
				preg_match_all("/([^>]+)php\.ini/",$phpinfostr,$mats);
				$path = $mats[1][1];
			}else{
				$ini= ini_get_all();
				$path = $ini['extension_dir']['local_value'];
				if(strpos($path, ':')===false){
					$browscap = $ini['browscap']['local_value'];
					if($browscap && strpos($browscap, ':')!==false){
						$path = substr($browscap, 0, strpos($browscap, ':')+1).$path;
					}
				}
			}			
			$php_path = str_replace('\\','/',$path);
			$php_path = str_replace(array('/ext/','/ext'),array('/','/'),$php_path);
			$real_path = $php_path.'php.exe';
		}else{
			$real_path = PHP_BINDIR.'/php';
		}

		if(strpos($real_path, 'ephp.exe') !== FALSE)
		{
			$real_path = str_replace('ephp.exe', 'php.exe',$real_path);
		}

		return $real_path;
	}
	
	public function ipCheck($ip){
	
		if(empty($ip)){
			return true;
		}
		$server_type = substr(strtolower(PHP_OS), 0, 3) == 'win' ? '' : 'linux';
		
		$serverips = $this->getClientMacAddr($server_type);
		$serverips[] = "127.0.0.1";
		
		$ips = explode(',', $ip);
		$exists = false;
		foreach ($serverips as $serverip)
		{
			if(in_array($serverip,$ips)){
				
				$exists = true;
				break;
			}
		}
		return $exists;
	
	}

	function GetClientMacAddr($os_type)
	{
		switch ( strtolower($os_type) )
		{
			case "linux":
				$this->forLinux();
				break;
			case "solaris":
				break;
			case "unix":
				break;
			case "aix":
				break;
			default:
				$this->forWindows();
				break;
		}

		$temp_array = array();

		$this->mac_addr = array();

		foreach ( $this->return_array as $value )
		{
			$mac = "/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/";

			if (preg_match($mac,$value,$temp_array))
			{

				$this->mac_addr[] = $temp_array[0];
			}
		}

		unset($temp_array);
		return $this->mac_addr;
	}
	
	function forWindows(){
		@exec("ipconfig /all", $this->return_array);

		if ($this->return_array)
			return $this->return_array;
		else{
			$ipconfig = $_SERVER["windir"]."\system32\ipconfig.exe";
			if ( is_file($ipconfig) )
				@exec($ipconfig." /all", $this->return_array);
			else
				@exec($_SERVER["windir"]."\system\ipconfig.exe /all", $this->return_array);
			return $this->return_array;
		}
	}
	function forLinux(){
		@exec("/sbin/ifconfig -a", $this->return_array);
		if(!$this->return_array)
			$this->return_array = array();
		return $this->return_array;
	}
	
	
	
	/**
	 * 执行命令获取结果 2019-01-12
	 * @param $cmd shell命令
	 */
	public function executeCmd($cmd)
	{
		$cmd = "{$cmd} 2>&1 ";
		$handle = popen($cmd,"r"); 
		$files = "";
		while(!feof($handle)) {
			$buffer = fgets($handle);
			$files .= $buffer;
		}
		pclose($handle);
		return $files;
	}
	
	/**
	 * 
	 * 2019-01-12
	 * @param string $cmd
	 * @param int $cmd_cont
	 */
	function chk_is_run($cmd, $cmd_cont){
		if (strpos($cmd_cont,$cmd) === false){
			return false;
		}else{
			return true;
		}
	}
	
	function win_run($cmd){
		$bat_str = "title oms_".md5($cmd)."\r\n".$cmd." \r\n exit";
		$bat_file_path =  CACHE_PATH .'Tmp/auto_task_bat/';
		if (!file_exists($bat_file_path)){
			mkdir($bat_file_path, 0777);
		}
		$bat_file = $bat_file_path . md5($cmd) .'.bat';
		file_put_contents($bat_file,$bat_str);
		popen('start /min '. $bat_file,'r');
		sleep(1);
	}
	
	/**
	 * 
	 * 获取windows系统下的定时任务列表 2019-01-12
	 * 
	 */
	public function getWinTaskList()
	{
		$handle = popen("tasklist -v", 'r');
		$cmd_cont = '';
		while(!feof($handle)) {
			$buffer = fgets($handle);
			$cmd_cont .= $buffer;
		}
		pclose($handle);
		return $cmd_cont;
	}

	/**
	 *
	 * windows系统下检查命令行时候允许 2019-01-12
	 * @param string $cmd
	 * @param int $cmd_cont
	 * @return bool
	 * 
	 */
	function win_chk_is_run($cmd, $cmd_cont){
		if(!$cmd_cont){
			$cmd_cont = $this->getWinTaskList();
		}
		$find_cmd = "oms_".md5($cmd);
	
		if (strpos($cmd_cont,$find_cmd) === false){
			return false;
		}else{
			return true;
		}
	}
	
	//自动KILL 超时任务,如果一个任务超过2个小时，不管有没有心跳信号，也要KILL掉
	function auto_kill_timeout_task(){
		if(substr(strtolower(PHP_OS), 0, 3) == 'win'){
			echo __("超时任务自动杀掉的功能,不支持windowns");
			return;
		}
		$cmd_str = $this->executeCmd("ps -ef|grep bin/php | grep -v grep");
		print_r($cmd_str);echo "\n\n";
		
		$task_str = $this->executeCmd("ps -ef|grep bin/php | grep -v grep | awk '{print $2,$5}'");
		$task_info = explode("\n",$task_str);
		foreach($task_info as $sub_info){
			if (empty($sub_info)){
				continue;
			}
			$_info = explode(" ",$sub_info);
			$_pid = (int)$_info[0];
			$_start_time = date('Y-m-d ').$_info[1].':00';
			$ret = MC('TaskManage','Lib/Core')->check_task_heartbeat_signal_is_time_out($_pid);
			print_r($ret);
			$kill_log_file = get_sys_cache_path().'/auto_task_kill.log';
			
			if ($ret['status']<0){
				$this->executeCmd("kill -9 {$_pid}");
				error_log($ret['msg'].$cmd_str."\n",3,$kill_log_file);
			}else{
				if (!empty($_info[1])){
					$_long_time = (time()-strtotime($_start_time))/60;
					if ($_long_time>=120){
						echo sprintf(__('pid= %s  开始时间 %s,任务超过2个小时. \n'),$_pid,$_start_time);
						$this->executeCmd("kill -9 {$_pid}");
						error_log('任务超过2个小时:'.$cmd_str."\n",3,$kill_log_file);
					}else{
						echo sprintf(__('pid= %s  开始时间 %s,任务已运行时长 %s 分钟. \n'),$_pid,$_start_time,round($_long_time));					
					}					
				}
			}
		}
	}
	
}