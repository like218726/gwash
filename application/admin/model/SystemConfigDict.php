<?php
namespace app\admin\model;

use think\Model;

class SystemConfigDict extends Model {

    /**
     * 获取参数字典列表
     * @return array 参数字典数组
     * @author wzj
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
     * 根据参数字典类型解析配置
     * @param  integer $type  参数字典类型
     * @param  string  $value 参数字典值
     * @author wzj
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
}