<?php
namespace app\admin\model;

use think\Model;

class SystemAdminUserData extends Model {
	protected $resultSetType = 'collection';
	protected $autoWriteTimestamp = true;
	protected $createTime = 'lastLoginTime';
	protected $updateTime = 'lastLoginTime';	
}