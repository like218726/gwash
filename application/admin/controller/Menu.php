<?php

namespace app\admin\controller;

class Menu extends Base {
	
	protected $result;
	
	public function _initialize(){
		$this->result = model('TableInfo')->getTableInfo('gwash.gwash_system_menu');
	}
	
    /**
     * 
     * 列表
     * @author licg
     * 
     */
    public function index() {
    	if ($this->request->isGet()) {
    		$this->assign('status_arr',$this->result['status']);
    		$this->assign('status','');
    		$this->assign('name',"");
    		return $this->fetch();
    	}
        
    }

    /**
     * 
     * AJAX获取菜单列表
     * 
     */
    public function ajaxGetIndex() {
    	if ($this->request->isGet()) {
	        $start = input('get.start', '0', 'trim') ? input('get.start', '0', 'trim') : 0;
	        $limit = input('get.length', '0', 'trim') ? input('get.length', '0', 'trim') : 20;
	        $draw = input('get.draw', '0', 'trim') ? input('get.draw', '0', 'trim') : 0; 
	        $where = array();
	        $name = input('get.name', '', 'trim');
	        $status = input('get.status', '', 'trim');
	        if ($name !='' ) {
	        	$where['name'] = ['like','%'.$name.'%'];
	        }

	        if (trim(input('status')) !='' ) {
	        	$where['status'] = $status;
	        }
	        
	        $total = model('SystemMenu')->where($where)->count();
	        $info = model('SystemMenu')->where($where)->limit($start, $limit)->select()->toArray();  
	        
	        foreach ($info as $k=>$list) { 
	       		$info[$k]['status'] = $list['status'] ? '隐藏' : '显示'; 	
	        	$menu_info = model('SystemMenu')->getMenuInfoById($list['fid'],$this->status_arr); 
	        	if ($list['level'] == '1') {
	        		$info[$k]['name'] = '|---'.$list['name'];
	        	} elseif ($list['level'] == '2') {
	        		$info[$k]['name'] = '|---|---'.$list['name'];
	        	} else {
	        		$info[$k]['name'] = $list['name'];
	        	}
	        	$info[$k]['level_name'] = $this->level[$list['level']];
	        	$info[$k]['fid'] = $menu_info['name'] ? $menu_info['name'] : '/';
	        } 
	        $data = array(
	            'draw'            => $draw,
	            'recordsTotal'    => $total,
	            'recordsFiltered' => $total,
	            'data'            => $info
	        );
	        $this->assign('status_arr',$this->result['status']);
    		$this->assign('status',"");
    		$this->assign('name',"");
	        $this->ajaxReturn($data, 'json');    		
    	}
    }
    
    /**
     * 
     * 新增
     * @author licg
     * 
     */
    public function add() {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['status'] = isset($data['status']) ? 1 : 0;  
            $menu_info = model('SystemMenu')->getMenuInfoById($data['fid']);
            $data['level'] = $menu_info['level']==0 ? 1 : ($menu_info['level']==1 ? 2 : 0);
            $res = model('SystemMenu')->insert($data);
            if ($res === false) {
                return $this->ajaxError('操作失败');
            } else {
            	return $this->ajaxSuccess('操作成功');
            }
        } else {
            $originList = model('SystemMenu')->where(['level'=>['in',['0','1']]])->order('sort asc')->select()->toArray();
            $this->assign('options', $originList);
            return $this->fetch();
        }
    }

    /**
     * 
     * 显示
     * @author licg
     * 
     */
    public function open() {
    	if ($this->request->isPost()) {
    		$id = input('post.id', '0', 'trim');
    		$result = model('SystemMenu')->where(['id'=>$id])->count();
    		if (!$result) {
    			return $this->ajaxError('参数非法');
    		}
    	    $res = model('SystemMenu')->where(array('id' => $id))->update(array('status' => 0));
	        if ($res === false) {
	            return $this->ajaxError('操作失败');
	        } else {
	            return $this->ajaxSuccess('操作成功');
	        }    		
    	}
    }

    /**
     * 
     * 隐藏
     * @author licg
     * 
     */
    public function close() {
    	if ($this->request->isPost()) {
    		$id = input('post.id', '0', 'trim');
    		$result = model('SystemMenu')->where(['id'=>$id])->count();
    		if (!$result) {
    			return $this->ajaxError('参数非法');
    		}
	    	$res = model('SystemMenu')->where(array('id' => $id))->update(array('status' => 1));
	        if ($res === false) {
	            return $this->ajaxError('操作失败');
	        } else {
	            return $this->ajaxSuccess('操作成功');
	        }
    	}
       
    }

    /**
     * 
     * 编辑
     * @author licg
     * 
     */
    public function edit() {
        if ($this->request->isGet()) {  
        	$id = input('get.id', '0', 'trim'); 
        	$originList = model('SystemMenu')->where(['level'=>['in',['0','1']]])->order('sort asc')->select()->toArray();
        	$listInfo = model('SystemMenu')->where(['id'=>$id])->find()->toArray();        	
            $this->assign('detail', $listInfo); 
            $this->assign('options', $originList);
            return $this->fetch("add");
        } elseif ($this->request->isPost()) {
            $postData = $this->request->post();
            $postData['status'] = isset($postData['status']) ? 1 : 0;  
            $menu_info = model('SystemMenu')->getMenuInfoById($postData['fid']);
            $data['level'] = $menu_info['level']==0 ? 1 : ($menu_info['level']==1 ? 2 : 0);
            $res = model('SystemMenu')->where(array('id' => $postData['id']))->update($postData);
            if ($res === false) {
                return $this->ajaxError('操作失败');
            } else {
                return $this->ajaxSuccess('操作成功');
            }
        }
    }

    /**
     * 
     * 删除
     * 
     */
    public function del() {
    	if ($this->request->isPost()) {
    		$id = input('post.id', '0', 'trim');
    		$result = model('SystemMenu')->where(['id'=>$id])->count();
    		if (!$result) {
    			return $this->ajaxError('参数非法');
    		}
    		
	    	$childNum = model('SystemMenu')->where(array('fid' => $id))->count();
	        if ($childNum) {
	            return $this->ajaxError("当前菜单存在子菜单,不可以被删除!");
	        } 
	        
	        $res = model('SystemMenu')->where(array('id' => $id))->delete();
    		if ($res === false) {
                return $this->ajaxError('操作失败');
            } else {
                return $this->ajaxSuccess('操作成功');
            }  		
    	}
    }

}