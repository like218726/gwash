<?php

namespace app\admin\controller;

use app\common\SystemAutoTask;

use app\admin\model\SystemTaskModel;
use think\Db;

class Task extends Base{
	
	/**
	 * 
	 * 列表
	 * 
	 */
	public function index(){ 
		if ($this->request->isGet()) { 
			$model = new SystemTaskModel();
			$this->assign('module', $model->moduleType());
			$this->assign("s_module",'');
			$this->assign('name','');
			$this->assign('is_on', SystemAutoTask::$AutoTask['is_on']);
			$this->assign('s_is_on','');
			$this->assign('base_path', $_SERVER['DOCUMENT_ROOT'].'/');  
			$BaseUrl = $_SERVER['SERVER_PORT']<>80 ? $_SERVER['REQUEST_SCHEME'].":".$_SERVER['SERVER_PORT']."://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'] : $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];	
			$this->assign('real_php_path', $this->realPath());	
			$row = model('SystemConfigDict')->where(array('code'=>'auto_task_is_on'))->find();
			if (!$row) {
				$total_is_on = '&#x1007;';
			} else {
				if ($row['value'] == '0') {
					$total_is_on = '&#x1007;';
				} else {
					$total_is_on = '&#xe616;';
				}
			}
			$this->assign("total_is_on", $total_is_on);
			$this->assign('BaseUrl', $BaseUrl);	
			return $this->fetch('index');
		}
	}
		
	/**
	 * 
	 * AJAX查询列表
	 * 
	 */
	public function ajaxGetIndex() {
		if ($this->request->isGet()) {
	        $postData = input();  
	        $start = $postData['start'] ? $postData['start'] : 0;
	        $limit = $postData['length'] ? $postData['length'] : 20;
	        $draw = $postData['draw'] ? $postData['draw'] : 20;
	        $where = array();
	        $type = input('get.type', '' ,'trim');
		    if ($type !='' ) {
	        	$where['type'] = ['like','%'.$type.'%'];
	        }
	        $task_name = input('get.task_name', '' ,'trim');
			if ($task_name !='' ) {
	        	$where['task_name'] = ['like','%'.$task_name.'%'];
	        }
	        $is_on = input('get.is_on', '' ,'trim');
			if ($is_on !='' ) {
	        	$where['is_on'] = ['like','%'.$is_on.'%'];
	        }		
	        $total = model('SystemTaskModel')->where($where)->count();
	        $info = model('SystemTaskModel')->where($where)->limit($start, $limit)->order('type,task_code,task_id desc')->select();
	  
	        if ($info) {
	        	$model = new SystemTaskModel();
	        	$type = $model->moduleType();
	        	$status = SystemAutoTask::$AutoTask['is_on'];
		        foreach ($info  as $k=>$row) { 
		        	$info[$k]['type'] = $type[$row['type']];
		        	$info[$k]['is_on'] = SystemAutoTask::$AutoTask['is_on'][$row['is_on']];
		        	$info[$k]['last_exec_time'] = $row['last_exec_time']==0 ? '' : date('Y-m-d H:i:s',$row['last_exec_time']);
		        }
	        }
	        $data = array(
	            'draw'            => $draw,
	            'recordsTotal'    => $total,
	            'recordsFiltered' => $total,
	            'data'            => $info
	        );
	        ajaxReturn($data, 'json');			
		}	
	}
	
	/**
	 * 
	 * 复制命令
	 */
	public function Copy()
	{ 
		if($this->request->isPost()){
			$task_id = input('task_id','0','trim');
			$model = new SystemTaskModel();  	
			$result = $model->copy($task_id); 
			exit(json_encode($result));
		}
	}
	
	/**
	 * 
	 * php真实路径
	 * 
	 */
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
	
	/**
	 * 刷新命令
	 * Enter description here ...
	 */
	public function RefreshCmd()
	{
		if ($this->request->isPost()) {
			$model = new SystemTaskModel();
			$result = $model->refreshCmd(); 
			if ($result['code'] === '101') { 
				return ajaxError("操作失败", 0, $result['msg']);
			} else {
				return ajaxSuccess("操作成功", 1, $result['msg']);
			}
		}
	}	
	
	/**
	 * 
	 *  开启/关闭
	 *  
	 */
	public function open() {
		if ($this->request->isPost()) {
			$ids = input('post.ids', '', 'trim'); 
			$ids = substr($ids, 0, -1);
			
			$is_on = input('post.is_on', '', 'trim');
			$result = model('SystemTaskModel')->where('task_id', 'in', $ids)->count();

			if ($result != count(explode(",", $ids))) {
				return ajaxError("参数非法");
			}
			$result = model('SystemTaskModel')->where('task_id', 'in', $ids)->update(['is_on'=>$is_on]);
			if ($result === false) { 
				return ajaxError("操作失败");
			} else {
				return ajaxSuccess("操作成功");
			}		
		}
	}
	
	/**
	 * 
	 *  开启/关闭
	 *  
	 */
	public function close() {
		if ($this->request->isPost()) {
			$ids = input('post.ids', '', 'trim'); 
			$ids = substr($ids, 0, -1);
			
			$is_on = input('post.is_on', '', 'trim');
			$result = model('SystemTaskModel')->where('task_id', 'in', $ids)->count();

			if ($result != count(explode(",", $ids))) {
				return ajaxError("参数非法");
			}
			$result = model('SystemTaskModel')->where('task_id', 'in', $ids)->update(['is_on'=>$is_on]);
			if ($result === false) { 
				return ajaxError("操作失败");
			} else {
				return ajaxSuccess("操作成功");
			}		
		}
	}

	/**
	 * 
	 * 批量修改时间
	 * 
	 */
	public function editTime() {
		if ( $this->request->isGet() ) {
			$ids = input('get.ids', '', 'trim');
			$ids = substr($ids, 0, -1);
			$this->assign('ids', $ids);
			return $this->fetch();
		} elseif ( $this->request->isPost() ) {
			$ids = input('post.task_id', '', 'trim');
			$lx_time = input('post.lx_time', '0', 'trim');
			$result = model('SystemTaskModel')->where('task_id', 'in', $ids)->count();

			if ($result != count(explode(",", $ids))) {
				return ajaxError("参数非法");
			}

			if (!$lx_time) {
                return ajaxError("轮询时间不能为空");
            }

			if (!isInt($lx_time)) {
                return ajaxError("轮询时间只能为整数");
            }

			$result = model('SystemTaskModel')->where('task_id', 'in', $ids)->update(['lx_time'=>$lx_time]);
			if ($result === false) { 
				return ajaxError("操作失败");
			} else {
				return ajaxSuccess("操作成功");
			}
		}
	}

	/**
	 * 
	 * 批量修改IP
	 * 
	 */
	public function editIp() {
		if ( $this->request->isGet() ) {
			$ids = input('get.ids', '', 'trim');
			$ids = substr($ids, 0, -1);
			$this->assign('ids', $ids);
			return $this->fetch();
		} elseif ( $this->request->isPost() ) {
			$ids = input('post.task_id', '', 'trim');
			$allow_ips = input('post.allow_ips', '0', 'trim');
			$result = model('SystemTaskModel')->where('task_id', 'in', $ids)->count();

			if ($result != count(explode(",", $ids))) {
				return ajaxError("参数非法");
			}

			if (!is_ip($allow_ips)) {
			    return ajaxError("IP格式非法");
            }

			$result = model('SystemTaskModel')->where('task_id', 'in', $ids)->update(['allow_ips'=>$allow_ips]);
			if ($result === false) { 
				return ajaxError("操作失败");
			} else {
				return ajaxSuccess("操作成功");
			}
		}
	}

	/**
	 * 
	 * 获取一行记录
	 * 
	 */
	public function GetRowByCode()
	{
		if($this->request->isPost()){
			$code = input('post.code', '', 'trim');
			$row = model('SystemConfigDict')->where(array('code'=>$code))->find();
			if(!$row){
				return ajaxError("没有总任务记录");
			}
			return ajaxSuccess('操作成功' ,1 ,$row);
		}
	}

	/**
	 * 更新总开关
	 * Enter description here ...
	 */
	public function UpdateAllStatus()
	{
		if ($this->request->isPost()) {
			$code = input('post.code', '', 'trim');
			$value= input('post.value', '', 'trim');
			$configDict = model('SystemConfigDict')->get(['code'=>$code]);
			if (empty($configDict)) {
				return ajaxError('没有总任务记录');
			} 
			$is_on = ($value==0) ? 1 : 0;
			Db::startTrans();
			try {				
                model('SystemConfigDict')->save(['value'=>$is_on],['code'=>$code]);
                model('SystemTaskModel')->killAll();
                $task_is_on = model('SystemConfigDict')->where(['code'=>$code])->value('value');
                Db::commit();
                return ajaxSuccess("操作成功", 1, ['is_on'=>$task_is_on]);
            } catch (\Exception $e) {
                Db::rollback();
                return ajaxError("操作失败:".$e->getMessage());
            }
		}
	}	
}