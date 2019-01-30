<?php
namespace app\admin\controller;

use think\Db;

use think\Controller;

class Test extends Controller{ 

    public function init() {
    	set_time_limit(0);
    }	
	
	public function Test() {
		$result =  Db::table('gwash_system_test_task')->order('id desc')->find(); 
		
		$arr = array('name'=>'张三','create_time'=>'2018-11-02 18:47:35');
		if (!$result) {
			Db::table('gwash_system_test_task')->insert($arr);
		} else {
			$data = array(
				'name'=>$result['name'].($result['id']+1),
				'create_time'=>date('Y-m-d H:i:s',time()),
			);
			Db::table('gwash_system_test_task')->insert($data);
		}	
		echo '没有要操作的数据';	
	}
}