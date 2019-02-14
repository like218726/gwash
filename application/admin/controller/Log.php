<?php
/**
 * @since   2017-04-13
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use think\Db;

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
	        $total = Db::name('system_admin_user_action')->where($where)->count();
	        $info = Db::name('system_admin_user_action')->where($where)->limit($start, $limit)->select();
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
     * 删除
     * 
     */
    public function del() {
    	if ($this->request->isPost()) {
    		$id = input('post.id','0','trim');
    		$result = Db::name('system_admin_user_action')->where('id', $id)->count();
    		if (!$result) {
    			return $this->ajaxError('参数非法');
    		}
	    	$res = Db::name('system_admin_user_action')->where('id', $id)->delete();
	        if ($res === false) {
	            return $this->ajaxError('操作失败');
	        } else {
	            return $this->ajaxSuccess('操作成功');
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
            $listInfo = Db::name('system_admin_user_action')->where('id', $id)->find();
            $this->assign('detail', $listInfo);
            return $this->fetch('showDetail');
        }
    }
}