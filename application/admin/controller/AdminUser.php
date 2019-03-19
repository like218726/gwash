<?php

namespace app\admin\controller;

class AdminUser extends Base { 
	
	protected $result;
	
	public function _initialize() {
		$this->result = model('TableInfo')->getTableInfo('gwash.gwash_system_admin_user');
	}
	
	/**
	 * 
	 * 列表
	 * 
	 */
    public function index() {
    	if ($this->request->isGet()) {
    		$this->assign('nickname','');
	    	$this->assign('status','');
	    	$this->assign('status_arr',$this->result['status']);
	        return $this->fetch();
    	}
    }	
	
    /**
     * 
     * Ajax 查询列表
     * 
     */
	public function ajaxGetIndex() {
		if ($this->request->isGet()) {
			$where = array();
			$start =  input('get.start','0','trim') ? input('get.start','0','trim') : 0;   
			$limit = input('get.length','0','trim') ? input('get.length','0','trim') : 20;
			$draw = input('get.draw','0','trim') ? input('get.draw','0','trim') : 0;

			$nickname = input('get.nickname','','trim');
			$status = input('get.status','','trim');
		
			if ($nickname != '') {
				$where['nickname'] = array('like','%'.$nickname.'%');
			}
			
			if ($status != '') {
				$where['a.status'] = $status;
			}
		
			$total = model('SystemAdminUser')
					->alias('a')
					->field('a.id,a.username,a.nickname,a.status,a.login_count,a.last_login,a.last_ip,a.last_city,c.name')
                    ->join('SystemAuthGroupAccess b','a.id = b.uid','left')
                    ->join('SystemAuthGroup c','b.groupId = c.id','left')
                    ->where($where)
                    ->count(); 

	        $listInfo = model('SystemAdminUser')
				        ->alias('a')
				        ->field('a.id,a.username,a.nickname,a.status,a.login_count,a.last_login,a.last_ip,a.last_city,c.name')
	                    ->join('SystemAuthGroupAccess b','a.id = b.uid','left')
	                    ->join('SystemAuthGroup c','b.groupId = c.id','left')
	                    ->order('id', 'desc')
	                    ->limit($start, $limit)
	                    ->where($where)
	                    ->select();  

			$superAdmin = config('USER_ADMINISTRATOR');
	        if ($listInfo) {
	        	foreach ($listInfo as $key=>$value) {       		
		        	if (in_array($value['id'],$superAdmin)){
		                $listInfo[$key]['name'] = '超级管理员';	                
		            }
	                $listInfo[$key]['last_login'] = $value['last_login'] ? date('Y-m-d H:i:s', $value['last_login']) : "";	
	                $listInfo[$key]['status'] = $value['status']== 1 ? '<span class="layui-btn">启用</span>' : '<span class="layui-btn layui-btn-danger">禁用<span>';
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
	        $this->assign('username',$nickname);
	        $this->assign('status_arr',$this->result['status']);
	        $this->assign('status',$status);
	        $this->ajaxReturn($data, 'json');			
		}	
	}

	
	/**
	 * 
	 * 添加
	 * 
	 */
    public function add() {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $data['nickname'] = input('post.nickname','','trim');
            $data['username'] = input('post.username','','trim');   
            $data['mobile'] = input('post.mobile','','trim');  
            $has = model('SystemAdminUser')->where(array('username' => $data['username']))->count(); 
            if ($has) {
                return $this->ajaxError('用户名已经存在，请重设！');
            }
			$data['password'] = 123456;
            $data['password'] = md5($data['password']);
            $data['regIp'] = get_client_ip();
            $data['regTime'] = time(); 
            $res = model('SystemAdminUser')->insertGetId($data);
            if ($res === false) {
            	return $this->ajaxError('操作失败');
            } else {
            	return $this->ajaxSuccess('操作成功');
            }
        } else {
            return $this->fetch();
        }
    }

    
    
    /**
     * 
     * 禁用
     * 
     */
    public function close() {
    	if ($this->request->isPost()) {
	        $id = input('post.id','0','trim');
	        $isAdmin = isAdministrator($id);
	        if ($isAdmin) {
	            return $this->ajaxError('超级管理员不可以被操作');
	        }
	        $result = model('SystemAdminUser')->where('id', $id)->find();
	        if (!$result) {
	        	return $this->ajaxError('参数非法');
	        }
	        $res = model('SystemAdminUser')->where('id', $id)->update(array('status' => 0));
	        if ($res === false) {
	        	return $this->ajaxError('操作失败');
	        } else {
	        	return $this->ajaxSuccess('操作成功');
	        }    		
    	}
    } 

    /**
     * 
     * 启用
     * 
     */
    public function open() {
    	if ($this->request->isPost()) {
	        $id = input('post.id','0','trim');
	        $isAdmin = isAdministrator($id);
	        if ($isAdmin) {
	            return $this->ajaxError('超级管理员不可以被操作');
	        }
    		$result = model('SystemAdminUser')->where('id', $id)->find();
	        if (!$result) {
	        	return $this->ajaxError('参数非法');
	        }	        
	        $res = model('SystemAdminUser')->where('id', $id)->update(array('status' => 1));
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
    	    $id = input('post.id','0','trim');
	        $isAdmin = isAdministrator($id);
	        if ($isAdmin) {
	            return $this->ajaxError('超级管理员不可以被操作');
	        }
    		$result = model('SystemAdminUser')->where('id', $id)->find();
	        if (!$result) {
	        	return $this->ajaxError('参数非法');
	        }
	        if ($result['status']) {
	        	return $this->ajaxError("只能删除禁用的管理员");
	        }	
	        $res = model('SystemAdminUser')->where(array('id' => $id,'status'=>0))->delete();
	        if ($res === false) {
	            return $this->ajaxError('操作失败');
	        } else {
	        	model('SystemAdminUserAction')->where('uid', $id)->delete();
	        	model('SystemAdminUserData')->where('uid', $id)->delete();	        	
	            return $this->ajaxSuccess('操作成功');
	        }    		
    	}
    }    
}