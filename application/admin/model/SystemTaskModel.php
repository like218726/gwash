<?php
namespace app\admin\model;
use think\Db;

use think\Model;
class SystemTaskModel extends Model{
    
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
				Db::name('system_auto_task')->where('task_code',$row['code'])->update($newData);
			}
		}
		$services = $datas;
		$datas = array_values($datas);
		Db::name('system_auto_task')->insertAll($datas, $options = array('task_code'), $replace = true);
		
		$rows = Db::name('system_auto_task')->select();
		
		foreach($rows as $row)
		{
			$code = $row['task_code'];
			if(!isset($services[$code])){
				Db::name('system_auto_task')->where('task_code', $code)->where('is_copy', 0)->delete();
			}
		}
		
		return array('code'=>'200','msg'=>"成功");
	}
	
	//复制记录
	public function copy($task_id)
	{  
		$row = Db::name('system_auto_task')->where('task_id',$task_id)->find();
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
		$id = Db::name('system_auto_task')->insertGetId($data);
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
		$rows = Db::name('system_auto_task')->where($where)->select();    
		$copy_num = 0;
		foreach($rows as $row){
			if($row['is_copy']>$copy_num){
				$copy_num = $row['is_copy'];
			}
		}
		return $copy_num;
	}	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
   public $table = 'home_auto_task';
   public $tableAcl = 'home_acl';
   public $tableShop = 'shop';
   public $tablePlatform = 'platform';
   public $moduleType;
   
	/**
	 * 系统任务列表
	 * Enter description here ...
	 * @param Array $request 请求的参数
	 * @return Array
	 */
	public function tasklist($request)
	{
		$result = $this->_buildWhere($request);	
		$rows = $this->db->selectLimitArr($result['sql'], $result['total'], $result['page'], $result['page_size'], $result['w_arr']);
		$arr = array();
		if(!$this->moduleType){
			$this->moduleType = $this->moduleType();
		}
		//$shops = getShop();
		foreach ($rows as $row) 
		{
			//$row['shop_ids'] = $row['shop_ids'] ? implode(',', array_filter(explode(',',$row['shop_ids']))) : $row['shop_ids'];
			$row['type'] = isset($this->moduleType[$row['type']]) ? $this->moduleType[$row['type']] : '';
			$arr[] = $row;
		}
		return array('rows' => (empty($arr) ? array() : $arr), 'total' => $result['total']);
	}
	
	/**
	 * 创建sql
	 * Enter description here ...
	 */
	public function _buildWhere($request)
	{
		$request['rows'] = !isset($request['rows']) && empty($request['rows']) ? 10 : $request['rows'];
		$request['page'] = !isset($request['page']) && empty($request['page']) ? 1 : $request['page'];
		$where = " WHERE 1 ";
		$w_arr = array();
		if (isset($request['task_name']) && $request['task_name']) {
			$where .= " and a.task_name like :task_name";
			$w_arr[':task_name'] = '%'.$request['task_name'].'%';
		}
		if (isset($request['task_code']) && $request['task_code']!=='' ) {
			$where .= " and a.`task_code`=:task_code";
			$w_arr[':task_code'] = $request['task_code'];
		}
		if (isset($request['type']) && $request['type']!=='' ) {
			$where .= " and a.`type` = :type";
			$w_arr[':type'] = $request['type'];
		}
		
		if (isset($request['is_on']) && $request['is_on']!=='' ) {
			$where .= " and a.`is_on` = :is_on";
			$w_arr[':is_on'] = $request['is_on'];
		}
		
		if(isset($request['shop_ids']) && $request['shop_ids']!==''){
			$where.= $this->getShopWhere($request['shop_ids'], $w_arr);
		}
		
		$sql = "SELECT count(*) FROM  $this->table a 
				$where ";
		$total = $this->db->getOne($sql, $w_arr);
		
		$sql = "SELECT a.task_id,a.cmd,a.task_name,a.lx_time,a.type ,a.process_num,a.shop_ids,a.platform_code,
			   a.is_on,if(a.last_exec_time,FROM_UNIXTIME(a.last_exec_time),'') as last_exec_time,a.allow_ips
			   FROM $this->table a
			   $where ORDER BY a.type,a.task_code,a.task_id desc";
		
		return array('sql' => $sql, 'total'=>$total, 'w_arr'=>$w_arr, 'page'=>$request['page'], 'page_size'=>$request['rows']);
	}
	
	//店铺查询条件
	public function getShopWhere($shop_ids, &$w_arr)
	{
		$where = "";
		if(!$shop_ids)
			return '';
			
		$shop_ids = explode(',', $shop_ids);
		foreach($shop_ids as $k=>$shop_id){
			$key = ':shop_id'.$k;
			$where[] = "a.shop_ids like $key";
			$w_arr[$key] = '%,'.$shop_id.',%';
		}
		return " and (".implode(' or ', $where).")";
	}
	
	
	/**
	 * 更新状态
	 * Enter description here ...
	 */
	public function cangeStatus($field, $value, $ids)
	{
		$ids = MC('SearchModel','App/Model/Common')->filter_sql_in($ids,'int');
		if($field=='is_on' && $value==0){
			$this->closeKillTask($ids);
		}
		return $this->save(array($field=>$value), "task_id in(".$ids.")");
	}
	
	/**
	 * 关闭杀进程
	 * Enter description here ...
	 */
	public function closeKillTask($ids)
	{
		$rows = $this->where("task_id in(".$ids.")")->getAll();
		$cmd = "ps -ef | grep php";
		$result = MC('Cli', 'Lib/Core')->linux_shell_run($cmd);
		$real_path = MC('Cli', 'Lib/Core')->realPath();
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
			$shop_id = implode(',', array_filter(explode(',',$row['shop_ids'])));
			$cmd = trim($row['cmd']);
			
			$exec_command = $real_path.' -f '.BASE_PATH.$cmd;
			
			//进程数
			if($process_num>0){
				$exec_command .=' --_g_run_child_task_num='.$process_num;
			}
			
			//店铺id
			if($shop_id){
				$exec_command .=' --shop_id='.$shop_id;
			}
			
			foreach($cmds_arr_new as $cmdItem)
			{
				if(strpos($cmdItem, $exec_command)!==false){
					//echo $cmdItem;echo '====';
					//echo $exec_command;
					//echo 'success';die;
					$arr = array_values(array_filter(explode(' ', $cmdItem)));
					$taskId = intval(trim($arr[1]));
					if($taskId){
						$cmd = "kill -9 ".$taskId;
						$result = MC('Cli', 'Lib/Core')->linux_shell_run($cmd);
						$cmd = '';
						break;
					}
				}
			}
		}
		return;
	}
	
	
	protected function createShopCmd($data, $plat_code, &$datas)
	{
		$cmd = $data['cmd'];
		$shops = getShop(array('platform_code'=>$plat_code, 'state'=>1));
		
		foreach($shops as $shop_id => $row)
		{
			$data['cmd'] = $cmd.' --shop_id='.$shop_id;
			$data['shop_id'] = $shop_id;
			$datas[] = $data;
		}
		
	}
	
	/**
	 * 从配置文件刷新生成命令到数据库
	 * Enter description here ...
	 */
	public function refreshCmd2()
	{
		require_once CONFIG_PATH.'/AutoTaskConfig.php';
		$app_path = APP_PATH;
		$datas = array();
		foreach($services as $row)
		{	
			if(isset($datas[$row['code']])){
				return $this->setError(-1, __('任务配置文件中的任务代码').$row['code'].__('重复了'));
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
				$sql = "update $this->table set cmd='".$row['exec']."',task_name=concat('".$row['name']."',is_copy) where is_copy>0 and type='".$row['type']."' and task_code='".$row['code']."'";
				$this->db->execute($sql);
			}
		}
		$services = $datas;
		$datas = array_values($datas);
		$this->massInsert($this->table, $datas, 2, 'task_name,cmd,type,description,process_num');
		$rows = $this->getAll();
		
		foreach($rows as $row)
		{
			$code = $row['task_code'];
			if(!isset($services[$code])){
				$sql = "delete from $this->table where task_code='$code' and is_copy=0";
				$this->db->execute($sql);
			}
		}
		
		return true;
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
	 * 批量修改
	 * Enter description here ...
	 * @param unknown_type $ids
	 * @param unknown_type $field
	 * @param unknown_type $field_value
	 */
	public function massUpdate($ids, $field, $field_value){
		if(!$ids)
			return ture;
		$task_ids = MC('SearchModel','App/Model/Common')->filter_sql_in($ids,'int');
		//echo '<hr/>task_ids<xmp>'.var_export($task_ids,true).'</xmp>';die;
		$this->save(array($field=>$field_value), " task_id in($task_ids)");
		return true;
	}
	
	/**
	 * 复制记录
	 */
	private function coptyNum2($task_code, $type)
	{
		$rows = $this->where(array('task_code'=>$task_code, 'type'=>$type))->getAll();
		
		$copy_num = 0;
		foreach($rows as $row){
			if($row['is_copy']>$copy_num){
				$copy_num = $row['is_copy'];
			}
		}
		return $copy_num;
	}
	
	
	//复制记录
	public function copy2($task_id)
	{
		$row = $this->getRow($task_id);
		if(!$row){
			return $this->setError(-1, __('复制错误'));
		}
		unset($row['task_id']);
		$copy_num = $this->coptyNum($row['task_code'], $row['type']);
		if($copy_num>=10){
			return $this->setError(-1, __('相同的任务已经存在10条，不允许再复制'));
		}
		$copy_num = $copy_num+1;
		$row['task_name'] = str_replace($row['is_copy'], "", $row['task_name']).$copy_num;
		$row['is_copy'] = $copy_num; 
		$row['shop_ids'] = '';
		$row['is_on'] = 0;
		$row['last_exec_time'] = '';
		return $this->save($row);
	}
	
	//更新绑定的店铺
	public function updateShopIds($task_id, $shop_ids)
	{
		if(!$shop_ids){
			$this->save(array('shop_ids'=>''), array('task_id'=>$task_id));
			return true;
		}
		$row = $this->getRow($task_id);
		$rows = $this->tasklist(array('shop_ids'=>$shop_ids, 'task_code'=>$row['task_code']));
		
		if($rows['total']==0){
			$shop_ids = $shop_ids ? ",".implode(',',explode(',', $shop_ids))."," : '';
			$this->save(array('shop_ids'=>$shop_ids), array('task_id'=>$task_id));
			return true;
		}else{
			$msg = array();
			$shops = array();
			foreach($rows['rows'] as $row){
				if($row['task_id']==$task_id)
					continue;
				$shops = array_merge($shops, explode(',', $row['shop_ids']));
			}
			$shops = array_flip(array_filter($shops));
			
			$shop_arr = getShop();
			$shop_ids = explode(',', $shop_ids);
			foreach($shop_ids as $shop_id){
				if(isset($shops[$shop_id])){
					$msg[] = SF('%s已经被其他相同任务绑定', $shop_arr[$shop_id]['shop_name']);
				}
			}
			if($msg){
				return $this->setError(-1, implode("<br/>",$msg));
			}
			$shop_ids = $shop_ids ? ",".implode(',', $shop_ids)."," : '';
			$this->save(array('shop_ids'=>$shop_ids), array('task_id'=>$task_id));
			return true;
		}
	}
	
	/**
	 * 杀死所有php进程
	 * Enter description here ...
	 */
	public function killAll()
	{
		$sql = "select task_id from home_auto_task where is_on=1";
		$ids = $this->db->getCols($sql);
		if(!$ids)
			return ;
		$ids = implode(',', $ids);
		$this->closeKillTask($ids);
	}
}