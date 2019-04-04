<?php
namespace app\admin\model;

use think\Model;

class SystemConfigDict extends Model {

    /**
     * 
     * 获取参数字典列表
     * @return array 参数字典数组
     * 
     */
    public function getDictLists()
    {
        $map = array();
        $data = $this->where($map)->field('type,name,value')->where(['status'=>1])->select();

        $config = array();
        if ($data && is_array($data)) {
            foreach ($data as $value) {
                $config[$value['name']] = $this->parseDict($value['type'], $value['value']);
            }
        }
        return $config;
    }


    /**
     * 
     * 根据参数字典类型解析配置
     * @param  integer $type  参数字典类型
     * @param  string  $value 参数字典值
     * 
     */
    private function parseDict($type, $value)
    {
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }
    
    /**
     * 
     * 根据模块ID查询模块名称
     * @param unknown_type $module_id
     */
    public function getModuleId($module_id) {
    	if ($module_id == 0) {
    		return '公共模块';
    	}
    	$result = model('SystemMenu')->where('id', $module_id)->value('name');
    	return $result;
    }
    
    /**
     * 
     * 获取模型类型
     * 
     */
    public function getModule() {
    	$result = model('SystemMenu')->where(['level'=>1, 'fid'=>['<>',3]])->select();
    	$data['0'] = '公共模块';
    	foreach ($result as $k=>$v) {
    		$data[$v['id']] = $v['name'];
    	}
    	return $data;
    }
}