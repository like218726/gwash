<?php

namespace app\common;

class SystemConfigDict {
	
	public static $ConfigDict = [
		'status' => ['0'=>'禁用', '1'=>'启用'],
		'type' => ['0'=>'数字', '1'=>'字符串','0'=>'文本'],
		'is_display' => ['0'=>'显示', '1'=>'隐藏'],
		'is_default' => ['0'=>'否', '1'=>'是'],
	];
}