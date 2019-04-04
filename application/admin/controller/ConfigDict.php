<?php
namespace app\admin\controller;

use app\common\SystemConfigDict;

class ConfigDict extends Base
{
	protected $type;
	
	public function _initialize() {
		$this->type = config('CONFIG_TYPE_LIST');
	}
	
    /**
     * 列表
     * 
     */
    public function index()
    {
    	if ($this->request->isGet()) {
    		$this->assign('name','');
    		$this->assign('status_arr',SystemConfigDict::$ConfigDict['status']);
    		$this->assign('status',"");
    		return $this->fetch();
    	}
    }

    /**
     * 
     *  AJAX查询列表
     *  
     */
    public function ajaxgetindex() {
    	if ($this->request->isGet()) {
			$where = array();
			$start = input('get.start', '0', 'trim') ? input('get.start', '0', 'trim') : 0;
			$limit = input('get.length', '0', 'trim') ? input('get.length', '0', 'trim') : 20;
			$draw = input('get.draw', '0', 'trim') ? input('get.draw', '0', 'trim') : 0;

			$name = input('get.name', '', 'trim');
			$status = input('get.status', '', 'trim');

			if ($name != '') {
				$where['name'] = ['like','%'.$name.'%'];
			}
			
			if ($status != '') {
				$where['status'] = $status;
			}
	
			$total = model('SystemConfigDict')->order('id', 'desc')->where($where)->count(); 

	        $listInfo = model('SystemConfigDict')->order('id', 'desc')->limit($start, $limit)->where($where)->select();  

	        if ($listInfo) {
	        	foreach ($listInfo as $key=>$value) {       		
	                $listInfo[$key]['status'] = SystemConfigDict::$ConfigDict['status'][$value['status']];
	                $listInfo[$key]['type'] = $this->type[$value['type']];
	                $listInfo[$key]['module_id'] = model('SystemConfigDict')->getModuleId($value['module_id']);
	        	}
	        } else {
	        	$listInfo = '';
	        }                    
	                   
	        $data = array(
	            'draw'            => $draw,
	            'recordsTotal'    => $total,
	            'recordsFiltered' => $total,
	            'data'            => $listInfo,
	        );
	        $this->assign('name',$name);
	        $this->assign('status',$status);
	        ajaxReturn($data, 'json');	    		
    	}
    }    
    
    /**
     * 
     * 添加
     * 
     */
    public function add()
    {
        if($this->request->isPost()){
            $data = input();
            $code_count = model('SystemConfigDict')->where('code', $data['code'])->count();
            if ( $code_count ) {
            	return ajaxError('操作失败: 参数代码已经存在');
            }
        	$name_count = model('SystemConfigDict')->where('name', $data['name'])->count();
            if ( $name_count ) {
            	return ajaxError('操作失败: 参数名称已经存在');
            }
            $result = model('SystemConfigDict')->insert($data);
            if($result === false) {
                return ajaxError('操作失败');
            } else {
                cache("DB_CONFIG_DICT_DATA",null);
                return ajaxSuccess('操作成功');
            }
        }
        $module_id = model('SystemConfigDict')->getModule();
        $type = config('CONFIG_TYPE_LIST'); 
		$this->assign("module_id",$module_id); 
		$this->assign("type",$type); 
		$detail['type'] = $this->type;
		$detail['module_id'] = $module_id;
		$detail['is_default'] = SystemConfigDict::$ConfigDict['is_default'];
		$this->assign('is_default', SystemConfigDict::$ConfigDict['is_default']);
		$detail['is_display'] = SystemConfigDict::$ConfigDict['is_display'];
		$this->assign('is_display', SystemConfigDict::$ConfigDict['is_display']);
		$this->assign('detail',$detail);
        return $this->fetch("config_dict/add");
    }

    /**
     * 
     * 编辑参数字典
     * 
     */
    public function edit()
    {
        if( $this->request->isGet() ) {
            $id = input('get.id', '0', 'trim');
	        $module_id = model('SystemConfigDict')->getModule();
	        $type = config('CONFIG_TYPE_LIST'); 
			$this->assign("module_id",$module_id); 
			$this->assign("type",$type); 
			$this->assign('is_default', SystemConfigDict::$ConfigDict['is_default']);
			$this->assign('is_display', SystemConfigDict::$ConfigDict['is_display']);            
            $detail = model('SystemConfigDict')->where('id', $id)->find();
            $this->assign('detail', $detail); 
            return $this->fetch('config_dict/add');
        } else if($this->request->isPost()){
            $data = $this->request->post(); 
            $data['status'] = 1;
            $res = model('SystemConfigDict')->save($data,['id'=>$data['id']]);
            if( $res === false ) {
                return ajaxError('操作失败');
            } else {
                cache("DB_CONFIG_DICT_DATA",null);
                return ajaxSuccess('操作成功');
            }

        }

    }

    /**
     * 
     * 删除参数字典
     * 
     */
    public function del()
    {
    	if ($this->request->isPost()) {
    		$id = input('post.id', 0 , 'trim');
    		$result = model('SystemConfigDict')->where('id', $id)->count();
    		if (!$result) {
    			return ajaxError('参数非法');
    		}
    		$res = model('SystemConfigDict')->where('id', $id)->delete();
	        if ($res === false) {
	            return ajaxError('操作失败');
	        } else {
	            cache("DB_CONFIG_DICT_DATA",null);
	            return ajaxSuccess('操作成功');
	        }
    	}
    }

    /**
     * 
     * 禁用
     * 
     */
    public function close()
    {
    	if ($this->request->isPost()) {
    		$id = input('post.id', 0 , 'trim');
    		$result = model('SystemConfigDict')->where('id', $id)->count();
    		if (!$result) {
    			return ajaxError('参数非法');
    		}
	    	$res = model('SystemConfigDict')->where('id', $id)->update(array('status' => 0));
	        if ($res === false) {
	            return ajaxError('操作失败');
	        } else {
	            cache("DB_CONFIG_DICT_DATA",null);
	            return ajaxSuccess('操作成功');
	        }
    	}
    }

    /**
     * 
     * 启用
     * 
     */
    public function open()
    {
    	if ($this->request->isPost()) {
    		$id = input('post.id', 0 , 'trim');
  
    		$result = model('SystemConfigDict')->where('id', $id)->count();
    		if (!$result) {
    			return ajaxError('参数非法');
    		}
	    	$res = model('SystemConfigDict')->where('id', $id)->update(array('status' => 1));
	        if ($res === false) {
	            return ajaxError('操作失败');
	        } else {
	            cache("DB_CONFIG_DICT_DATA",null);
	            return ajaxSuccess('操作成功');
	        }
    	}
    }

}