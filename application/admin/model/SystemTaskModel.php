<?php

namespace app\admin\model;

use think\Loader;
use think\Model;
use util\Cli;

class SystemTaskModel extends Model{
	
     protected $table = 'gwash_system_auto_task';
     
	/**
	 * 从配置文件刷新生成命令到数据库
	 * Enter description here ...
	 */
	public function refreshCmd()
	{
		require_once CONF_PATH.'admin/config/AutoTaskConfig.php';
		$app_path = APP_PATH;
		$datas = array();
		foreach($services as $row)
		{	
			if(isset($datas[$row['code']])){
				return array('code'=>'101', 'msg'=>'任务配置文件中的任务代码'.$row['code'].'重复了');
			}
			$newData = array();
			$newData['task_name'] = $row['name'];
			$newData['task_code'] = $row['code'];
			$newData['type'] = $row['type'];
			$newData['lx_time'] = $row['time'];
			$newData['description'] = $row['desc'];
			$newData['cmd'] = $row['exec'];
			$newData['platform_code'] = isset($row['plat_code']) ? $row['plat_code'] : '';
			$newData['process_num'] = isset($row['process']) && $row['process']>0 ?  $row['process'] : 0;
			$datas[$newData['task_code']] = $newData;
			
			//刷新复制的命令
			if($newData['platform_code']){
				$this->where('task_code',$row['code'])->update($newData);
			}
		}
		$services = $datas;
		$datas = array_values($datas);
		$this->insertAll($datas, $options = array('task_code'), $replace = true);
		
		$rows = $this->select();
		
		foreach($rows as $row)
		{
			$code = $row['task_code'];
			if(!isset($services[$code])){
				$this->where('task_code', $code)->where('is_copy', 0)->delete();
			}
		}
		
		return array('code'=>'200','msg'=>"成功");
	}
	
	//复制记录
	public function copy($task_id)
	{  
		$row = $this->where('task_id',$task_id)->find();
		if(!$row){
			return array('code'=>'-1', 'msg'=>'复制错误');
		}
		
		unset($row['task_id']);
		$copy_num = $this->coptyNum($row['task_code'], $row['type']);
		if($copy_num>=10){
			return array('code'=>'-1', 'msg'=>'相同的任务已经存在10条，不允许再复制');
		}
			
		$copy_num = $copy_num + 1;	
		$data = [];
		$data['task_code'] = $row['task_code'];	
		$data['task_name'] = str_replace($row['is_copy'], "", $row['task_name']).$copy_num;
		$data['type'] = $row['type'];
		$data['cmd'] = $row['cmd'];
		$data['platform_code'] = $row['platform_code'];
		$data['description'] = $row['description'];
		$data['is_copy'] = $copy_num; 
		$data['shop_ids'] = '';
		$data['is_on'] = 0;
		$data['last_exec_time'] = '';			
		$id = $this->insertGetId($data);
		if ($id === false) {
			return array('code'=>'-1', 'msg'=>'复制失败');
		} else {
			return array('code'=>'200', 'msg'=>'复制成功');
		}
	}
		
	/**
	 * 复制记录
	 */
	private function coptyNum($task_code, $type)
	{
		$where['task_code'] = $task_code;
		$where['type'] = $type;
		$rows = $this->where($where)->select();    
		$copy_num = 0;
		foreach($rows as $row){
			if($row['is_copy']>$copy_num){
				$copy_num = $row['is_copy'];
			}
		}
		return $copy_num;
	}	
	

	/**
	 * 
	 *  模型类型
	 *  
	 */
	public function moduleType()
	{
		require_once APP_PATH.'admin/config/AutoTaskConfig.php';
		return $moduleType;
	}

    /**
     * 杀死所有php进程
     * Enter description here ...
     */
    public function killAll()
    {
        $result = $this->where(['is_on'=>'1'])->select();
        $ids = [];
        if (empty($result)) {
            return ;
        } else {
            foreach ($result as $k=>$v) {
                $ids[$k]['task_id'] = $v['task_id'];
            }
        }
        $ids = implode(',', $ids);
        $this->closeKillTask($ids);
    }

    /**
     * 关闭杀进程
     * Enter description here ...
     */
    public function closeKillTask($ids)
    {
        $rows = $this->where('task_id', 'in', $ids)->select();
        $cmd = "ps -ef | grep php";
        Loader::import('util.Cli',EXTEND_PATH,'.php');
        $cli = new Cli();
        $result = $cli->linux_shell_run($cmd);
        $real_path = $cli->realPath();
        @$cmds = $result['cmd_cont'];
        if(!$cmds)
            return;

        $cmds_arr = str_getcsv($cmds, "\n");
        $cmds_arr_new = array();
        foreach($cmds_arr as $row)
        {
            $cmds_arr_new[] = $row;
        }

        foreach($rows as $row){

            $id = (int)$row['task_id'];
            $process_num = $row['process_num'];
            $cmd = trim($row['cmd']);

            $exec_command = $real_path.' -f '.BASE_PATH.$cmd;

            //进程数
            if($process_num>0){
                $exec_command .=' --_g_run_child_task_num='.$process_num;
            }

            foreach($cmds_arr_new as $cmdItem)
            {
                if(strpos($cmdItem, $exec_command)!==false){
                    $arr = array_values(array_filter(explode(' ', $cmdItem)));
                    $taskId = intval(trim($arr[1]));
                    if($taskId){
                        $cmd = "kill -9 ".$taskId;
                        $result = $cli->linux_shell_run($cmd);
                        $cmd = '';
                        break;
                    }
                }
            }
        }
        return true;
    }
}