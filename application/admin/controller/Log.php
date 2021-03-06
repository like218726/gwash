<?php

namespace app\admin\controller;

class Log extends Base {
	
	/**
	 * 
	 * 列表
	 * 
	 */
    public function index() {
    	if ($this->request->isGet()) {
    		return $this->fetch();
    	}
    }

    /**
     * 
     * AJAX 查询列表
     * 
     */
    public function ajaxGetIndex() {
    	if ($this->request->isGet()) {
	        $postData = input();
			$start = input('get.start','0','trim') ? input('get.start','0','trim') : 0;   
			$limit = input('get.length','0','trim') ? input('get.length','0','trim') : 20;
			$draw = input('get.draw','0','trim') ? input('get.draw','0','trim') : 0;
	        $where = array();
	        if (isset($postData['type']) && !empty($postData['type'])) {
	            if (isset($postData['keyword']) && !empty($postData['keyword'])) {
	                switch ($postData['type']) {
	                    case 1:
	                        $where['url'] = array('like', '%' . $postData['keyword'] . '%');
	                        break;
	                    case 2:
	                        $where['nickname'] = array('like', '%' . $postData['keyword'] . '%');
	                        break;
	                    case 3:
	                        $where['uid'] = $postData['keyword'];
	                        break;
	                }
	            }
	        }
	        $total = model('SystemAdminUserAction')->where($where)->count();
	        $info = model('SystemAdminUserAction')->where($where)->limit($start, $limit)->select();
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
     * 删除
     * 
     */
    public function del() {
    	if ($this->request->isPost()) {
    		$id = input('post.id','0','trim');
    		$result = model('SystemAdminUserAction')->where('id', $id)->count();
    		if (!$result) {
    			return ajaxError('参数非法');
    		}
	    	$res = model('SystemAdminUserAction')->where('id', $id)->delete();
	        if ($res === false) {
	            return ajaxError('操作失败');
	        } else {
	            return ajaxSuccess('操作成功');
	        }
    	}
    }

    /**
     * 
     * 详情
     * 
     */
    public function showDetail() {
        if ($this->request->isGet()) { 
        	$id = input('get.id','0','trim'); 
            $listInfo = model('SystemAdminUserAction')->where('id', $id)->find();
            $this->assign('detail', $listInfo);
            return $this->fetch('showDetail');
        }
    }
}