<?php
/**
 * 
 * 系统任务控制类
 *
 */
namespace app\admin\controller;

use think\Db;

use app\admin\model\SystemTaskModel;

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
			$this->assign('is_on',array(''=>'请选择','0'=>'关闭','1'=>'开启'));
			$this->assign('s_is_on','');
			$this->assign('base_path', $_SERVER['DOCUMENT_ROOT'].'/');  
			$BaseUrl = $_SERVER['SERVER_PORT']<>80 ? $_SERVER['REQUEST_SCHEME'].":".$_SERVER['SERVER_PORT']."://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'] : $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];	
			$this->assign('real_php_path', $this->realPath());	
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
	        $draw = $postData['draw'];
	        $where = array();
		    if (trim(input('type')) !='' ) {
	        	$where['type'] = ['like','%'.trim(input('type')).'%'];
	        }
			if (trim(input('task_name')) !='' ) {
	        	$where['task_name'] = ['like','%'.trim(input('task_name')).'%'];
	        }
			if (trim(input('is_on')) !='' ) {
	        	$where['is_on'] = ['like','%'.trim(input('is_on')).'%'];
	        }		
	        $total = Db::name('system_auto_task')->where($where)->count();
	        $info = Db::name('system_auto_task')->where($where)->limit($start, $limit)->order('type,task_code,task_id desc')->select();
	  
	        if (is_array($info)) {
	        	$model = new SystemTaskModel();
	        	$type = $model->moduleType();
	        	$status = ['0'=>'关闭','1'=>'开启'];
		        foreach ($info  as $k=>$row) {
		        	$info[$k]['type'] = $type[$row['type']];
		        	$info[$k]['is_on'] = $status[$row['is_on']];
		        	$info[$k]['last_exec_time'] = $row['last_exec_time']==0 ? '' : date('Y-m-d H:i:s',$row['last_exec_time']);
		        }
	        }
	        $data = array(
	            'draw'            => $draw,
	            'recordsTotal'    => $total,
	            'recordsFiltered' => $total,
	            'data'            => $info
	        );
	        $this->ajaxReturn($data, 'json');			
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
				exit(json_encode($result));
			} else {
				exit(json_encode($result));
			}
		}
	}	
	
	/**
	 * 
	 *  开启/关闭
	 *  
	 */
	public function ChangeStatus() {
		if ($this->request->isPost()) {
			$ids=$this->getParam("ids");
			$value = $this->getParam('value');
			$taskModel = new TaskModel();
			$result = $taskModel->cangeStatus('is_on', $value, $ids);	
			$this->showMessage(__('操作成功'), 1);
			exit;			
		}
	}
}