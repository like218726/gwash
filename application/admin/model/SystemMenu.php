<?php

namespace app\admin\model;

use think\Db;

use think\Model;

class SystemMenu extends Model {
	protected $resultSetType = 'collection';
	
	/**
	 * 
	 * 通过菜单ID查询其信息
	 * 
	 */
	public function getMenuInfoById($id) {
		$result = Db::name('system_menu')->where(['id'=>$id])->find();
		return $result;
	}
}