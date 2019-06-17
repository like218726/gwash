<?php

namespace app\admin\controller;

use util\Cli;

class AutoTask extends Base{
	protected $run_log = 0; //是否记录自动运行日志

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

		$row = model('SystemTaskModel')->where('task_id',$id)->find();
		
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
		model('SystemTaskModel')->where('task_id',$id)->update(array('last_exec_time'=> $current_timestamp));
		$cli= new Cli();
		$task_id = $cli->run($command_script);
		if ($task_id === false){
			echo "启动命令行失败";die;
		}
		$BaseUrl = $_SERVER['SERVER_PORT']<>80 ? $_SERVER['REQUEST_SCHEME'].":".$_SERVER['SERVER_PORT']."://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'] : $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];	
		
		$this->assign('task_id', $task_id);
		$this->assign('BaseUrl', $BaseUrl);
		return $this->fetch('timer/RunOneCmdScript');
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
}