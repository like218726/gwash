<?php

namespace app\admin\controller;

use util\Cli;

use think\Controller;

class Runcmdscript extends Controller{
   	  
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