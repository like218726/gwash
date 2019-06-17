<?php

namespace app\admin\controller;

use util\Cli;

use think\Controller;

class Runcmdscript extends Controller{

	function index() {
		$cli = new Cli();
		$cmd = $this->request->param();
		$cmd = $cli->decode($cmd);
		$task_id = $cli->run($cmd);
		if ($task_id === false) {
			return ajaxError("启动命令失败");
		}
		$this->assign('task_id', $task_id);
		$this->fetch('Timer/Runcmdscript');	
	}

	/**
	 * 
	 * 获取记录
	 * 2019-01-29
	 * 
	 */
	function GetRunResult(){
		$task_id = $_REQUEST['task_id'];
		$log_file_offset = (int)@$_REQUEST['log_file_offset']; 
		$cli = new Cli();
		$ret = $cli->getRunResult($task_id,$log_file_offset); 
		echo $ret;
	}
	
	
}