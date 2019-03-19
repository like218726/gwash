<?php

namespace app\admin\model;

use think\Db;

use think\Model;

class TableInfo extends Model {
	protected $tableName = '';
	
	/**
	 * 
	 * 获取表字段信息
	 * @param string $tableName
	 * @return array
	 * 
	 */
	public function getTableInfo($tableName) {
		$tableName_arr = explode(".", $tableName);
		if (count($tableName_arr)>1) {
			$database = trim($tableName_arr['0']);
			$table = trim($tableName_arr['1']);
			$sql = "select * from information_schema.COLUMNS where table_schema = '$database' and table_name = '$table'"; 
		} else {
			$sql = "select * from information_schema.COLUMNS where table_name = '$tableName'";
		}
		$result = Db::query($sql); 
		
		if ($result) {
			$data = array();
			foreach ($result as $key=>$value) {
				if (strstr($value['COLUMN_COMMENT'],':')) {
					$arr = explode(":", $value['COLUMN_COMMENT']);
					foreach (explode(",", trim($arr['1'])) as $kk=>$vv) {
						$temp = explode(".", $vv);
						$data[$value['COLUMN_NAME']][trim($temp['0'])] = trim($temp['1']);
					}
				}
			}
			return $data;
		}
	}
}