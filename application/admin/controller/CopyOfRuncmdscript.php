<?php

namespace app\admin\controller;

use think\Loader;

use think\Controller;

class Runcmdscript extends Controller{
   	  
	function actionIndex(){
		$cmd = $_REQUEST['cmd'];
		$cmd = MC('Crypt','Lib/Core')->decode($cmd);
		//echo $cmd;die;
		$task_id = MC('Cli','Lib/Core')->run($cmd);
		if ($task_id === false){
			echo __("启动命令行失败");die;
		}
		$this->assign('task_id', $task_id);
		$this->display('Timer/Runcmdscript');
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
		Loader::import('Cli', EXTEND_PATH, '.php');  
		$cli = new \Cli();
		$ret = $cli->getRunResult($task_id,$log_file_offset); 
		echo $ret;
	}

	function actionRunSdk(){
		$req = $this->getParams();
		$db = DbFactory::getDbInstantiation();
		$sql = "select platform_code from shop where shop_id = ".(int)$req['shop_id'];
		$platform_code = $db->getOne($sql);
		$_platform_code = ucfirst(strtolower($platform_code));
		$method = $req['method'];
		unset($req['method']);
		
		foreach($req as $k=>$v){
			if (empty($v)){
				unset($req[$k]);
			}
		}

		$cmd_path = MC('Cli','Lib/Core/')->get_cmd_path();
		$cmd = $cmd_path ."sdk.php Sdk/{$_platform_code} {$method} ";
		$cmd_params = array();
		foreach($req as $k=>$v){
			$cmd_params[] = "--{$k}=\"{$v}\"";
		}
		$cmd .= join(' ',$cmd_params);
		//echo $cmd;die;		
		$cmd = MC('Crypt','Lib/Core')->encode($cmd);
		$url = BASE_URL_PATH.'/index.php/Timer/Runcmdscript?cmd='.urlencode($cmd);
		echo "<script>location.href='".$url."';</script>";
		die;
	}
	
}