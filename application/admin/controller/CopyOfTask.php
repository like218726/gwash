<?php
/**
 * 
 * 系统任务控制类
 *
 */
namespace app\admin\controller;
class Task extends Base{
	
	public function index(){
		echo '33333';
	}
	
	
	
	public $commandDir = 'System'; //系统任务文件夹名称
	public $taskListPath = 'Home/Configuration/Task/TaskList';
	public $taskForm = 'Home/TaskForm';
	
	/**
	 * 任务列表
	 * Enter description here ...
	 */
	public function actionIndex(){
		$model = $this->loadModel('Home/TaskModel');
		if($this->isAjaxRequest()){
			
			$list = $model->tasklist($this->getParams());
			exit(json_encode($list));
		}

		$this->assign('module', $model->moduleType());
		$this->assign('shopHtml', $this->shopSelect(), 2);
		$this->assign('base_path', BASE_PATH);
		$this->assign('real_php_path', $this->realPath());
		$this->assign('title', __('系统任务'));
		$this->display($this->taskListPath);
	}
	
	

	public function getShop($code)
	{
		if(!$code)
			return false;
		return $this->loadModel('Platform/Shop')->where(array('shop_code'=>$code))->getRow();
	}
	
	/**
	 * 刷新命令
	 * Enter description here ...
	 */
	public function actionRefreshCmd()
	{
		$model = $this->loadModel('Home/TaskModel');
		$result = $model->refreshCmd();
		if(!$result){
			$this->showMessage($model->getErrorMsg(), 0);
		}
		$this->showMessage(__('刷新成功'), 1);
	}
	
	//复制记录
	public function actionCopy()
	{
		if($this->isPost()){
			$model = $this->loadModel('Home/TaskModel');
			$task_id = $this->getParam('task_id');
			$result = $model->copy($task_id);
			if(!$result){
				$this->showMessage($model->getErrorMsg(), 0);
			}
			$this->showMessage(1, 1);
		}
	}
	
	/**
	 * 批量修改
	 * Enter description here ...
	 */
	public function actionMassUpdate()
	{
		$model = $this->loadModel('Home/TaskModel');
		$field = $this->getParam('field');
		$ids = $this->getParam('ids');
		if($this->isPost())
		{
			$field_value = $this->getParam($field);
			$result = $model->massUpdate($ids, $field, $field_value);
			$this->showMessage(__('操作成功'), 1);
			die;
		}
		$this->assign('field', $field);
		$this->assign('field_name', $field=='lx_time' ? __('轮询时间') : ($field=='allow_ips' ? __('允许的ip') : ''));
		$this->display('Home/TaskMassForm');
	}
	
	
	/**
	 * 添加任务
	 * Enter description here ...
	 */
	public function actionAdd()
	{   
		if($this->isPost()){
			$model = $this->loadModel('Home/TaskModel');
			$params = $this->getParams();
            $params['cmd'] = $this->commandDir.'/'.$params['controller'].' '.$params['action'].' '.$params['paramstr'];
            $params['type'] = $this->type;
            
            if($this->type==2 && $params['shop_codes']){
            	$shop_codes = explode(',', $params['shop_codes']);
            	foreach($shop_codes as $code)
            	{
            		if(!$code ){
						continue;            		
            		}
            		if($model->where(array('task_name'=>$params['task_name'], 'shop_code'=>$code))->getTotal())
            			continue;
            		$shop = $this->getShop($code);
            		if(!$shop)
            			continue;
            		$data['shop_code'] = $code;
            		$data['cmd'] = $this->commandDir.'/'.$params['controller'].' '.$params['action']." --shop_id=".$shop['shop_id'].($params['paramstr'] ? ' '.$params['paramstr'] : '');
            		$data['module_id'] = $params['module_id'];
            		$data['task_name'] = $params['task_name'];
            		$data['lx_time'] = $params['lx_time'];
            		$data['allow_ips'] = $params['allow_ips'];
            		$data['is_on'] = $params['is_on'];
            		$data['type'] = $this->type;
            		$result = $model ->save($data);
            		
            	}
            	$this->showMessage(__('添加成功'), 1);
            	die;
            }else{
            	
            	unset($params['controller']);
                unset($params['action']);
                unset($params['paramstr']);
				$result = $model ->save($params);
				if(!$result){
					$this->showMessage(__('添加失败'), 0);
				}
				$this->showMessage(__('添加成功'), 1);
            }
			exit;
		}
		
		$this->assignModule();
		$this->assign('controller', $this->_getController());
		$this->assign('user_code', $this->loadModel('Home/UserModel')->getUserCode());
		$this->display($this->taskForm);
	}
	
	/**
	 * 系统任务所有类文件
	 * Enter description here ...
	 */
	private function _getController(){
		$file = new FileUtil();
		$files = $file->readDir($this->_commandPath(), false, array('php'));
		
		$controllerFiles = array();
		foreach($files as $filePath){
			//require_once $filePath;
			$fileName = str_replace(array($this->_commandPath().'/','.php'), array('',''), $filePath);
			$fileNamePre = str_replace($this->router->getDefaultController(), "", $fileName);
			$controllerFiles[$fileNamePre] = $fileName;
			
		}
		return $controllerFiles;
	}
	
	private function _commandPath(){
		return $this->getCommandPath().'/'.$this->commandDir;
	}
	
	/**
	 * 获取一个类的所有公共方法
	 * Enter description here ...
	 */
	public function actionGetAction()
	{
		if($this->isAjaxRequest()){
			
			$controller = $this->getParam('controller');
			if(!$controller){
				$this->showMessage('', 0);
			}
			$controllerName = $controller.$this->router->getDefaultController();
			$controllerFile = $controllerName.'.php';
			require $this->_commandPath().'/'.$controllerFile;
			if(!class_exists($controllerName)){
				$this->showMessage('', 0);
			}
			$actions = $this->getClassActions($controllerName);
			$this->showMessage($actions, 1);
		}
	}
	
	public function getClassActions($className)
	{
		$actionAll = get_class_methods($className);
		$actionFilter = get_class_methods('Controller');
		
		$action = array();
		foreach($actionAll as $item){
			if(!in_array($item, $actionFilter)){
				if(substr($item, 0, 6)!='action'){
					continue;
				}
				$action[] = array('action'=>$item, 'act'=>substr($item, 6));
			}
		}
		return $action;
	}
	
	
	/**
	 * 编辑
	 * Enter description here ...
	 */
	public function actionUpdate()
	{   
		$task_id = $this->getParam('task_id');
		$actionUrl = $this->baseUrl().'/Home/TaskSystem/Update/task_id/'.$task_id;
		if($this->isPost())
		{
			$params = $this->getParams();
			if(!$params['controller'] || !$params['action']){
				$this->showMessage(__('没有提交执行类或执行方法'), 0);
			}
		    $params['cmd'] = $this->commandDir.'/'.$params['controller'].' '.$params['action'].' '.$params['paramstr'];
            
		    unset($params['controller']);
            unset($params['action']);
            unset($params['paramstr']);
			$result = $this->loadModel('Home/TaskModel')->save($params, array('task_id'=>$task_id));
			if(!$result){
				$this->showMessage(__('没有做任何修改'), 0, $actionUrl);
			}	
			$this->showMessage(__('更新成功'), 1, $actionUrl);
			exit;
		}
		
		$this->assign('action', $actionUrl);
		$this->assign('controller', $this->_getController());
		$this->assignModule();
		$data = $this->loadModel('Home/TaskModel')->getRow($task_id);	
		
		$cmd_arr = $this->_parseCmd($data['cmd']);
		$data['controller'] = $cmd_arr['controller'];
		$data['action'] = $cmd_arr['action'];
		$data['paramstr'] = $cmd_arr['paramstr'];
		
		$this->assign('data', $data);
		$this->display('Home/TaskForm');
	}
	
	private function _parseCmd($cmd)
	{
		$arr = array();
		$cmd = preg_replace('/\s{2,}/', ' ', $cmd);
		$cmd_arr =  explode(' ', $cmd);
		$arr['controller'] = str_replace($this->commandDir.'/', '', $cmd_arr[0]);
		$arr['action'] = $cmd_arr[1];
		unset($cmd_arr[0]);
		unset($cmd_arr[1]);
		$arr['paramstr'] = implode('', $cmd_arr);
		return $arr;
	}
	
    public function actionAutoAddTask(){
             $result = $this->loadModel('Home/TaskModel')->AutoAddTask();      
    	     echo $result;    
    }	
	
	/**
	 * 删除
	 * Enter description here ...
	 */
	public function actionDelete()
	{
		if($this->isAjaxRequest()){
			$taskIds = $this->getParam('ids');
			$taskIds = implode(',', array_map('intval', explode(',', $taskIds)));
			if(!$taskIds)
				exit;
			$result = $this->loadModel('Home/TaskModel')->delete('task_id in ('.$taskIds.')');
			if(!$result){
				$this->showMessage(__('删除失败'), 0);
			}	
			$this->showMessage(__('删除成功'), 1);
			exit;
		}
	}
	
	
	/**
	 * 批量更新
	 */
	public function actionChangeStatus(){
		if($this->isPost()){
			$ids=$this->getParam("ids");
			$value = $this->getParam('value');
			$taskModel = new TaskModel();
			$result = $taskModel->cangeStatus('is_on', $value, $ids);	
			$this->showMessage(__('操作成功'), 1);
			exit;
		}
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
	
	/**
	 * 获取一条记录
	 * Enter description here ...
	 */
	public function actionGetRow()
	{
		if($this->isPost()){
			$taskModel = new TaskModel();
			$task_id = $this->getParam('task_id');
			$row = $taskModel->getRow($task_id);
			$this->showMessage($row, 1);
		}
	}
	
	public function actionCopyCmd()
	{
		$this->showMessage(1, 1);
	}
	
	/**
	 * 更新任务绑定的店铺
	 */
	public function actionUpdateShops()
	{
		if($this->isPost())
		{
			$task_id = $this->getParam('task_id');
			$shop_ids = $this->getParam('shop_ids');
			$taskModel = new TaskModel();
			$result = $taskModel->updateShopIds($task_id, $shop_ids);
			if(!$result){
				$this->showMessage($taskModel->getErrorMsg(), 0);
			}
			$this->showMessage(1, 1);
		}
	}

	/**
	 * 获取一行记录
	 * Enter description here ...
	 */
	public function actionGetRowByCode()
	{
		if($this->isPost()){
			$code = $this->getParam('code');
			$model = new Config();
			$row = $model->where(array('code'=>$code))->getRow();
			if(!$row){
				$this->showMessage('没有总任务记录', 0);
			}
			$this->showMessage($row, 1);
		}
	}
	
	/**
	 * 更新总开关
	 * Enter description here ...
	 */
	public function actionUpdateAllStatus()
	{
		if($this->isPost()){
			$model = new Config();
			$taskModel = new TaskModel();
			$code = $this->getParam('code');
			$update = array('code'=>$code);
			$row = $model->where(array('code'=>$code))->getRow();
			if(!$row){
				$this->showMessage('没有总任务记录', 0);
			}
			$is_on = $row['value'] ? 0 : 1;
			$result = $model->save(array('value'=>$is_on), $update);
			if($result){
				if($is_on==0){
					$taskModel->killAll();
				}
				$this->showMessage('操作成功', 1);
			}
			$this->showMessage('操作失败', 0);
		}
	}
    
}