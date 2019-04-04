<?php
/*
$arr = \util\Cli::linux_shell_run('ls -al');
$arr = \util\Cli::realPath();
echo '<hr/>arr<xmp>'.var_export($arr,true).'</xmp>';//die;
*/
namespace util;
class Cli{
	public static $sys_cache_path;
	
	public static function realPath()
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

	public static function linuxAutoKillRun($cmd){
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

    public static function get_cli_log_path(){
        $_path1 = get_sys_cache_path().'/Tmp/';
        $php_cli_log_path = $_path1.'php_cli_log/';
        if (!file_exists($php_cli_log_path)){
            mkdir($php_cli_log_path,0777,true);
        }
        return $php_cli_log_path;
    }
    
	public static function getLogFile($task_id){
		$log_file = self::get_cli_log_path() . $task_id .'.log';
		return $log_file;
	}

	public static function getRunResult($task_id,$log_file_offset){
		$log_file = self::get_cli_log_path() . $task_id .'.log';
		$handle = fopen($log_file, "r");
		if($handle === false){
			$ret = '任务执行情况的缓存文件打开失败'.$log_file;
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

	public static function get_cmd_path(){
		$exec_command = self::realPath() . ' -f ' . ROOT_PATH;
		return $exec_command;
	}

	public static function linux_shell_run($cmd,$cd_path='',$err_redirect=1){
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
	
	public static function awk_shell_run($cmd,$cd_path='',$err_file_name){
		$ret = self::linux_shell_run($cmd,$cd_path,0);
		if ($ret['status']<0){
			return $ret;
		}
		$check_ret = self::check_shell_err($err_file_name);
		if ($check_ret !== false){
			return array('status'=>-1,'msg'=>'awk_run_err:'.$check_ret);
		}
		return $ret;
	}
	
	public static function check_shell_err($err_file_name){
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
	
	/**
	_g_run_task_id = 任务的ID号 -- 任务的ID号,每个任务都不一样,用于生成输出缓存的文件名
	_g_run_tag = 任务的标识,命令行MD5值 -- 对于业务处理内容一样的任务，这个值是一样的
	*/
	public static function run($exec_command,$run_params = array(),$append_params = array()){
		preg_match('/\.php ([\w,\/,_,-]+) ([\w,_,-]+)/',$exec_command,$mats);
		if (!empty($mats[1]) && !empty($mats[2])){
			$default_run_task_id = str_replace('/','_',$mats[1]).'_'.$mats[2].'_'.uniqid();			
		}else{
			$default_run_task_id = uniqid();
		}
		$task_id = $g_params['_g_run_task_id'] = isset($run_params['run_task_id']) ? $run_params['run_task_id'] : $default_run_task_id;
		$_g_log_file_id = isset($run_params['run_log_file_id']) ? $run_params['run_log_file_id'] : $task_id; 
		$g_params = array();
		$log_file = self::getLogFile($_g_log_file_id);		
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
			self::winRun($_run_exec_command,$log_file);
		}else{
			popen($_run_exec_command."  1>>{$log_file} 2>>{$log_file} &",'r');
		}
		return $task_id;
	}
    
	public static function winRun($cmd,$log_file){
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
	
}
