<?php
namespace app\admin\controller;

use think\Db;

class System extends Base
{
	/**
	 * 
	 * 列表
	 * 
	 */
    public function index()
    {
        $config = model('SystemConfig')->find()->toArray(); 
        $this->assign('config',$config);
        return $this->fetch();
    }

    /**
     * 
     * 保存
     * 
     */
    public function save()
    {
        $config = $this->request->post(); 
        unset($config['file']);
        $config['site_logo'] = trim($config['site_logo']);
        if (empty($config['id'])){
            $res = model('SystemConfig')->add($config);
        } else {
            $res = model('SystemConfig')->update($config,array('site_url'=>$config['site_url']));
        }

        if ($res === false){
        	return $this->ajaxError('保存失败');
        }
        return $this->ajaxSuccess('保存成功');
    }

    /**
     * 
     * 清空缓存
     * 
     */
    public function clear()
    {
        if (delete_dir_file(CACHE_PATH) && delete_dir_file(TEMP_PATH)
        	&& delete_dir_file(LOG_PATH )) {
            $this->success('清除缓存成功');
        } else {
            $this->error('清除缓存失败');
        }  	
    }
}