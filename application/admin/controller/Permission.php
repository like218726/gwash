<?php

namespace app\admin\controller;

/**
 * 权限管理控制器
 * @since   2018-11-07
 * @author  licg
 */
use think\Db;

use think\console\Input;

use think\Request;

class Permission extends Base {

	/**
	 * 
	 * 列表
	 * 
	 */
    public function index() {
    	if ($this->request->isGet()) {
			$listInfo = Db::name('system_auth_group')->select();
	        $this->assign('list', $listInfo);
	        $this->assign('name','');
	        $this->assign('status_arr',[''=>'请选择','0'=>'禁用','1'=>'正常']);
	        $this->assign('status','');
	        return $this->fetch();    		
    	}
    }

    /**
     * 
     * AJAX查询列表
     * 
     */
    public function ajaxGetIndex() {
		if ($this->request->isGet()) {
			$where = [];
			$start = trim(input('start/d'));
			$lenght = trim(input('length/d'));
			$start = $start ? $start : 0;
			$limit = $lenght ? $lenght : 20;
			$draw = trim(input('draw')) ? trim(input('draw')) : 0;

			$name = trim(input('post.name/s'));
			$status = trim(input('post.status/d'));
		
			if ($name != '') {
				$where['name'] = ['like','%'.$name.'%'];
			}
			
			if ($status != '') {
				$where['status'] = $status;
			}
		
			$total = Db::name('system_auth_group')
                    ->where($where)
                    ->count(); 

	        $listInfo = Db::name('system_auth_group')
	                    ->order('id', 'desc')->limit($start, $limit)
	                    ->where($where)
	                    ->select();  

	        if ($listInfo) {
	        	foreach ($listInfo as $key=>$value) {       		
	                $listInfo[$key]['status'] = $value['status']== 1 ? '正常' : '禁用';
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
	        $this->assign('status_arr',[''=>'请选择','0'=>'禁用','1'=>'正常']);
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
        	$data['name'] = trim(input('post.name/s'));
        	$data['description'] = trim(input('post.description/s')); 
        	$count = Db::name('system_auth_group')->where(['name'=>$data['name']])->count();
        	if ($count) {
        		return $this->ajaxError('权限组已存在');
        	}
            $res = Db::name('system_auth_group')->insert($data);
            if ($res === false) {
                $this->ajaxError('操作失败');
            } else {
                $this->ajaxSuccess('操作成功');
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
    		$id = trim(input('post.id/d'));
    		$result = Db::name('system_auth_group')->where(['id'=>$id])->count();
    		if (!$result) {
    			return $this->ajaxError('参数非法');
    		}
    		$res = Db::name('system_auth_group')->where(array('id' => $id))->update(array('status' => 0));
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
    		$id = trim(input('post.id/d'));
    		$result = Db::name('system_auth_group')->where(['id'=>$id])->count();
    		if (!$result) {
    			return $this->ajaxError("参数非法");
    		}
	    	$res = Db::name('system_auth_group')->where(array('id' => $id))->update(array('status' => 1));
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
     * 
     */
    public function edit() {
        if ($this->request->isGet()) {
        	$id = trim(input('get.id/d'));
            $detail = Db::name('system_auth_group')->where(array('id' => $id))->find();
            $this->assign('detail', $detail);
            return $this->fetch('add');
        } elseif ($this->request->isPost()) {
        	$id = trim(input('post.id/d'));
        	$data['name'] = trim(input('post.post.name/s'));
        	$data['description'] = trim(input('post.description'));
    	    $result = Db::name('system_auth_group')->where(['id'=>$id])->count();
    	    if (!$result) {
    	    	return $this->ajaxError('参数非法');   	    	
    	    }         	
            $res = Db::name('system_auth_group')->where(array('id' => $id))->update($data);
            if ($res === false) {
                return $this->ajaxError('操作失败');
            } else {
                return $this->ajaxSuccess('操作成功');
            }
        } else {
            return $this->ajaxError('非法操作');
        }
    }

    /**
     * 
     * 删除
     * 
     */
    public function del() {
    	if ($this->request->isPost()) {
    		$id = trim(input('id'));
    	    $result = Db::name('system_auth_group')->where(['id'=>$id])->count();
    	    if (!$result) {
    	    	return $this->ajaxError('参数非法');   	    	
    	    }   	    
	        $res = Db::name('system_auth_group')->where(array('id' => $id))->delete();
	        if ($res === false) {
	            return $this->ajaxError('操作失败');
	        } else {
	        	return $this->ajaxSuccess('操作成功');
	        }    		
    	}
    }

    /**
     * 将管理员加入组的组列表显示
     */
    public function group() {
        if ($this->request->isGet()) {
            $data = $this->request->post();
            if (!$data['uid']) {
            	 $this->error('用户ID不能为空');
            }
            if (!isset($data['groupAccess'])) {
            	return $this->ajaxError("授权内容不能为空");
            }
            $groupAccess = array_keys($data['groupAccess']);
            $groupAccess = implode(',', $groupAccess);
            $oldArr = model('SystemAuthGroupAccess')->where(array('uid' => $data['uid']))->find();
            if ($oldArr) {
            	$group = model('SystemAuthGroup')->where(['id'=>$oldArr['groupId']])->find();
            	if ($group['status'] == 0) {
            		return $this->ajaxError('你所属权限组已禁用');
            	}
                $insert = Db::name('system_auth_group_access')->where(array('uid' => $data['uid']))->update(array('groupId' => $groupAccess));
            } else {
                $insert = Db::name('system_auth_group_access')->insertGetId(array('uid' => $data['uid'], 'groupId' => $groupAccess));
            }
            if ($insert) {
                $this->success('授权成功');
            } else {
                $this->error('授权失败');
            }
        } elseif ($this->request->isGet()) {    
			$uid = trim(input('id'));
            $groupAccess = model('SystemAuthGroupAccess')->where(array('uid' => $uid))->find();
            $groupAccess = explode(',', $groupAccess['groupId']); 
            $allGroup = model('SystemAuthGroup')->select()->toArray();
            $this->assign('allGroup', $allGroup);
            $this->assign('groupAccess', $groupAccess);
            $this->assign('uid',$uid);
            return $this->fetch();
        } else {  
            $this->error('非法操作');
        }
    }

    /**
     * 显示当前组下面全部的用户
     */
    public function member() {
    	if ($this->request->isGet()) {
    		$groupId = trim(input('id/d'));
            $uidArr = array();
            $allGroups = Db::name('system_auth_group_access')->select();  
            foreach ($allGroups as $allGroup) {
                $gidArr = explode(',', $allGroup['groupId']);
                if (in_array($groupId, $gidArr)) {
                    $uidArr[] = $allGroup['uid'];
                }
            } 
            if (!empty($uidArr)) {
                $res = Db::name('system_admin_user')
                	   ->alias('tp_user')
                	   ->where(array('tp_user.id' => array('in', $uidArr)))
                	   ->join('gwash_system_admin_user_data tp_user_data', 'tp_user.id = tp_user_data.uid', 'left')
                	   ->field('tp_user.id,tp_user_data.uid,tp_user.username,tp_user.nickname,tp_user_data.loginTimes,tp_user_data.lastLoginIp,tp_user_data.lastLoginTime,tp_user.status')
                	   ->select();         
            } else {
                $res = array();
            } 
            $this->assign('list', $res);
            return $this->fetch();    		
    	} else {
    		return $this->ajaxError('非法操作');
    	}
    }

    /**
     * 删除指定组下面的指定用户
     */
    public function delMember() {
        if ($this->request->isPost()) {
            $uid = trim(input('post.uid/d'));
            $groupId = trim(input('post.groupId/d'));
            $oldInfo = Db::name('system_auth_group_access')->where(array('uid' => $uid))->find();
            $oldGroupArr = explode(',', $oldInfo['groupId']);
            $key = array_search($groupId, $oldGroupArr);
            unset($oldGroupArr[$key]);
            $newData = implode(',', $oldGroupArr);
            $res = Db::name('system_auth_group_access')->where(array('uid' => $uid))->update(array('groupId' => $newData));
            if ($res === false) {
                return $this->ajaxError('操作失败');
            } else {
                return $this->ajaxSuccess('操作成功');
            }
        } else {
            return $this->ajaxError('非法操作');
        }
    }

    /**
     * 当前用户组权限节点配置
     */
    public function rule() {
        if ($this->request->isPost()) {
        	$postData = input();
            $groupId = trim(input('post.groupId/d')); 
            if (!isset($postData['rule'])) {
            	return $this->error('权限不能为空');
            }
            $needAdd = array();
            $has = Db::name('system_auth_rule')->where(array('groupId' => $groupId))->select();
            $hasRule = array_column($has, 'url');
            $needDel = array_flip($hasRule);
            foreach ($postData['rule'] as $key => $value) {
                if (!empty($value)) {
                    if (!in_array($value, $hasRule)) {
                        $data['url'] = $value;
                        $data['groupId'] = $groupId;
                        $needAdd[] = $data;
                    } else {
                        unset($needDel[$value]);
                    }
                }
            }
            if (count($needAdd)) {
                Db::name('system_auth_rule')->insertAll($needAdd);
            }
            if (count($needDel)) {
                $urlArr = array_keys($needDel);
                Db::name('system_auth_rule')->where(array('groupId' => $groupId, 'url' => array('in', $urlArr)))->delete();
            }
            return $this->ajaxSuccess('操作成功');
        } elseif ($this->request->isGet()) {
        	$groupId = trim(input('id/d'));
            $has = Db::name('system_auth_rule')->where(array('groupId' => $groupId))->select();
            $hasRule = array_column($has, 'url');
            $originList = Db::name('system_menu')->order('sort desc')->select();

            $list = listToTree($originList);
            $this->assign('hasRule', $hasRule);
            $this->assign('list', $list);
            return $this->fetch();
        } else {
            return $this->ajaxError('非法操作');
        }
    }

}