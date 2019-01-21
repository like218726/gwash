<?php
namespace app\admin\controller;

use think\Db;

class ConfigDict extends Base
{
	public $status_arr = [''=>'请选择','0' => '禁用','1' => '启用'];
	public $type;
	public $status = '';
	
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
    		$this->assign('status_arr',$this->status_arr);
    		$this->assign('status',$this->status);
    		$this->assign('type',$this->type);
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
			$where = [];
			$start = trim(input('post.start/d'));
			$lenght = trim(input('post.length/d'));
			$start = $start ? $start : 0;
			$limit = $lenght ? $lenght : 20;
			$draw = trim(input('post.draw')) ? trim(input('post.draw')) : 0;

			$name = trim(input('name/s'));
			$status = trim(input('status'));

			if ($name != '') {
				$where['name'] = ['like','%'.$name.'%'];
			}
			
			if ($status != '') {
				$where['status'] = $status;
			}
	
			$total = Db::name('system_config_dict')->order('create_time desc')
                    ->where($where)
                    ->count(); 

	        $listInfo = Db::name('system_config_dict')
	                    ->order('id', 'desc')->limit($start, $limit)
	                    ->where($where)
	                    ->select();  

	        if ($listInfo) {
	        	foreach ($listInfo as $key=>$value) {       		
	                $listInfo[$key]['status'] = $this->status_arr[$value['status']];
	                $listInfo[$key]['type'] = $this->type[$value['type']];
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
	        $this->assign('status_arr',$this->status_arr);
	        $this->assign('status',$status);
	        $this->ajaxReturn($data, 'json');	    		
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
            $data['create_time'] = time();
            if(Db::name('system_config_dict')->insert($data)){
                cache("DB_CONFIG_DICT_DATA",null);
                return $this->ajaxSuccess('添加成功');
            } else {
                return $this->ajaxError('操作失败');
            }

        }
        $type = config('CONFIG_TYPE_LIST');
		$this->assign("type",$type); 
		$detail['type'] = $this->type;
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
            $id = trim(input('get.id/d'));
            if( $id ){
                $detail = Db::name('system_config_dict')->where(array('id' => $id))->find();
                $this->assign('detail', $detail);
                return $this->fetch('config_dict/add');
            }else{
                $this->redirect('config_dict/add');
            }
        } else if($this->request->isPost()){
            $data = $this->request->post();
            $data['id'] = trim($data['post.id/d']);
            $data['update_time'] = time();
            $res = Db::name('system_config_dict')->where(array('id' => $data['id']))->update($data);
            if( $res === false ) {
                return $this->ajaxError('操作失败');
            } else {
                cache("DB_CONFIG_DICT_DATA",null);
                return $this->ajaxSuccess('编辑成功');
            }

        }

    }

    /**
     * 删除参数字典
     * @author wzj
     *  2018/3/13
     */
    public function del()
    {
    	if ($this->request->isPost()) {
    		$id = trim(input('post.id/d'));
    		$result = Db::name('system_config_dict')->where(['id'=>$id])->count();
    		if (!$result) {
    			return $this->ajaxError('参数非法');
    		}
    		$res = Db::name('system_config_dict')->where(array('id' => $id))->delete();
	        if ($res === false) {
	            return $this->ajaxError('操作失败');
	        } else {
	            cache("DB_CONFIG_DICT_DATA",null);
	            return $this->ajaxSuccess('操作成功');
	        }
    	}
    }

    /**
     * 禁用
     * @author wzj
     * 2018/3/12
     */
    public function close()
    {
    	if ($this->request->isPost()) {
    		$id = trim(input('post.id/d'));
    		$result = Db::name('system_config_dict')->where(['id'=>$id])->count();
    		if (!$result) {
    			return $this->ajaxError('参数非法');
    		}
	    	$res = Db::name('system_config_dict')->where(array('id' => $id))->update(array('status' => 0));
	        if ($res === false) {
	            return $this->ajaxError('操作失败');
	        } else {
	            cache("DB_CONFIG_DICT_DATA",null);
	            return $this->ajaxSuccess('操作成功');
	        }
    	}
    }

    /**
     * 启用
     * @author
     * 2018/3/12
     */
    public function open()
    {
    	if ($this->request->isPost()) {
    		$id = trim(input('post.id/d'));
  
    		$result = Db::name('system_config_dict')->where(['id'=>$id])->count();
    		if (!$result) {
    			return $this->ajaxError('参数非法');
    		}
	    	$res = Db::name('system_config_dict')->where(array('id' => $id))->update(array('status' => 1));
	        if ($res === false) {
	            return $this->ajaxError('操作失败');
	        } else {
	            cache("DB_CONFIG_DICT_DATA",null);
	            return $this->ajaxSuccess('操作成功');
	        }
    	}
    }

}