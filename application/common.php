<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http：//thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http：//www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author： 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * @Action    调试打印
 * @Param     $var      需要打印的值
 *            $method   需要打印的方式
 *            $exit     是否停止程序继续执行
 * @Return    void
 */
use PHPMailer\src\PHPMailer;
use PHPMailer\src\Exception;
use think\Hook;
function xdebug($var, $exit = false, $method = true)
{
    echo '<meta content-type: "text/html" charset="utf-8" />';
	echo ' <pre>';
    $method ? print_r($var) : var_dump($var);
    echo '</pre> ' . '<hr style="color：red">' . '<br>';
    
	exit;
    
}

/**
 * 
 * 邮件发送
 * @param unknown_type $sender 发件人
 * @param unknown_type $server 服务器
 * @param unknown_type $receiver 收件人
 * @param unknown_type $receiver_name 收件人名称
 * @param unknown_type $is_auth 是否授权
 * @param unknown_type $password 密码/授权码
 * @param unknown_type $is_ssl 是否加密发送
 * @param unknown_type $port 加密端口,端口
 * @param unknown_type $subject 主题
 * @param unknown_type $body 内容
 * @param unknown_type $altbody 该属性的设置是在邮件正文不支持HTML的备用显示
 * @param unknown_type $attachment 附件
 */
function send_email ($sender, $server, $receiver, $receiver_name=[], $is_auth=0, $password, $is_ssl=0, $port=25, $subject, $body, $altbody, $attachment) {
	vendor('PHPMailer.src.PHPMailer');
	vendor('PHPMailer.src.Exception');
	vendor('PHPMailer.src.SMTP');
	$mail = new PHPMailer(true);
	try {
	    //Server settings
	    $mail->SMTPDebug = 2;                                 // Enable verbose debug output
	    $mail->isSMTP();                                      // Set mailer to use SMTP
	    $mail->Host = $server;  // Specify main and backup SMTP servers
	    if ($is_auth == 1) {
	    	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	    }
	    $mail->Username = $sender;                 // SMTP username
	    $mail->Password = $password;                           // SMTP password
	    if ($is_ssl == 1) {
		    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		    $mail->Port = $port;                                    // TCP port to connect to
	    } else {
	    	$mail->Port = $port; 
	    }
	    
	    //Recipients
	    if ($receiver_name) {
	    	foreach ($receiver as $k=>$v) {
	    		$mail->setFrom($v, $receiver_name[$k]);
	    		$mail->addAddress($v);
	    	}
	    } else {
	    	$mail->setFrom($v);               // Name is optional
	    	$mail->addAddress($v);
	    }

	    //Attachments
	    if ($attachment) {
	    	//需要解决附件为图片,下载后无法打开的问题
//	    	foreach ($attachment as $k=>$v) {
//	    		$mail->addAttachment($v['attachment_file'], $v['attachment_name']);         // Add attachments
//	    	}
	    }
	
	    //Content
	    $mail->isHTML(true);                                  // Set email format to HTML
	    $mail->Subject = $subject;
	    $mail->Body    = $body;
	    $mail->AltBody = $altbody;
	
	    $mail->send();
	    return ['code'=>'200', 'msg'=>'Message has been sent'];
	} catch (Exception $e) {
		return ['code'=>'101', 'msg'=>'Message could not be sent. Mailer Error:'. $mail->ErrorInfo];
	}	 
}

/**
 * Ajax方式返回数据到客户端
 * @access protected
 * @param mixed $data 要返回的数据
 * @param String $type AJAX返回数据格式
 * @param int $json_option 传递给json_encode的option参数
 * @return void
 */
function ajaxReturn($data,$type='',$json_option=0) {
	if(empty($type)) $type  =   config('DEFAULT_AJAX_RETURN');
	switch (strtoupper($type)){
		case 'JSON' :
			// 返回JSON数据格式到客户端 包含状态信息
			header('Content-Type:application/json; charset=utf-8');
			exit(json_encode($data,$json_option));
		case 'XML'  :
			// 返回xml格式数据
			header('Content-Type:text/xml; charset=utf-8');
			exit(xml_encode($data)); 
		case 'JSONP':
			// 返回JSON数据格式到客户端 包含状态信息
			header('Content-Type:application/json; charset=utf-8');
			$handler  =   isset($_GET[config('VAR_JSONP_HANDLER')]) ? $_GET[config('VAR_JSONP_HANDLER')] : config('DEFAULT_JSONP_HANDLER');
			exit($handler.'('.json_encode($data,$json_option).');');  
		case 'EVAL' :
			// 返回可执行的js脚本
			header('Content-Type:text/html; charset=utf-8');
			exit($data);            
		default     :
			// 用于扩展其他返回格式数据
			Hook::listen('ajax_return',$data); 			
	}
} 

/**
 * Ajax正确返回，自动添加debug数据
 * @param $msg
 * @param array $data
 * @param int $code
 */
function ajaxSuccess( $msg, $code = 1, $data = array() ){
	$returnData = array(
		'code' => $code,
		'msg' => $msg,
		'data' => $data
	);
	return json($returnData);
}

/**
 * Ajax错误返回，自动添加debug数据
 * @param $msg
 * @param array $data
 * @param int $code
 */
function ajaxError( $msg, $code = 0, $data = array() ){
	$returnData = array(
		'code' => $code,
		'msg' => $msg,
		'data' => $data
	);
	return json($returnData);
}

/**
 * 循环删除目录和文件
 * @param string $dir_name
 * @return bool
 */
function delete_dir_file($dir_name)
{
    $result = false;
    if (is_dir($dir_name)) {
        if ($handle = opendir($dir_name)) {
            while (false !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    if (is_dir($dir_name . DS . $item)) {
                        delete_dir_file($dir_name . DS . $item);
                    } else {
                        unlink($dir_name . DS . $item);
                    }
                }
            }
            closedir($handle);
            if (rmdir($dir_name)) {
                $result = true;
            }
        }
    }

    return $result;
}

/**
 * 
 * 获取系统缓存路径
 * 
 */
function get_sys_cache_path(){
	return RUNTIME_PATH;
}

/**
 * 把返回的数据集转换成Tree
 * @param $list
 * @param string $pk
 * @param string $pid
 * @param string $child
 * @param string $root
 * @return array
 */
function listToTree($list, $pk='id', $pid = 'fid', $child = '_child', $root = '0') {
    $tree = array();
    if(is_array($list)) {
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            }else{ 
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    } 
    return $tree;
}

function formatTree($list, $lv = 0, $title = 'name'){
    $formatTree = array();
    foreach($list as $key => $val){
        $title_prefix = '';
        for( $i=0;$i<$lv;$i++ ){
            $title_prefix .= "|---";
        }
        $val['lv'] = $lv;
        $val['namePrefix'] = $lv == 0 ? '' : $title_prefix;
        $val['showName'] = $lv == 0 ? $val[$title] : $title_prefix.$val[$title];
        if(!array_key_exists('_child', $val)){
            array_push($formatTree, $val);
        }else{
            $child = $val['_child'];
            unset($val['_child']);
            array_push($formatTree, $val);
            $middle = formatTree($child, $lv+1, $title); //进行下一层递归
            $formatTree = array_merge($formatTree, $middle);
        }
    }
    return $formatTree;
}

/**
 * 
 * xml格式化
 * @param $data
 * @param $root
 * @param $item
 * @param $attr
 * @param $id
 * @param $encoding
 * 
 */
function xml_encode($data, $root='think', $item='item', $attr='', $id='id',$encoding='utf-8') {
    if(is_array($attr)){
        $_attr = array();
        foreach ($attr as $key => $value) {
            $_attr[] = "{$key}=\"{$value}\"";
        }
        $attr = implode(' ', $_attr);//implode — 将一个一维数组的值转化为字符串
    }
    $attr   = trim($attr);
    $attr   = empty($attr) ? '' : " {$attr}";
    $xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";//  \"  表示转义双引号
    $xml   .= "<{$root}{$attr}>";//$attr根节点属性
    $xml   .= data_to_xml($data, $item, $id);
    $xml   .= "</{$root}>";
    return $xml;
}

/**
 * 
 * 数组转xml
 * @param unknown_type $data
 * @param unknown_type $item
 * @param unknown_type $id
 */
function data_to_xml($data, $item='item', $id='id') {
    $xml = $attr = '';
    foreach ($data as $key => $val) {
        if(is_numeric($key)){
            $id && $attr = " {$id}=\"{$key}\"";
            $key  = $item;
        }
     $xml    .=  "<{$key}{$attr}>";
     $xml  .=  (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
     $xml    .=  "</{$key}>";
    }
    return $xml;
}

/**
 * 判断是否是系统管理员
 * @param mixed $uid
 * @return bool
 */
function isAdministrator( $uid = '' ){
    if( empty($uid) ) $uid = session('uid'); 
    if( is_array(config('USER_ADMINISTRATOR')) ){
        if( is_array( $uid ) ){ 
            $m = array_intersect( config('USER_ADMINISTRATOR'), $uid );
            if( count($m) ){
                return TRUE;
            }
        }else{ 
            if( in_array( $uid, config('USER_ADMINISTRATOR') ) ){  
                return TRUE;
            }
        }
    }else{
        if( is_array( $uid ) ){
            if( in_array(config('USER_ADMINISTRATOR'),$uid) ){
                return TRUE;
            }
        }else{
            if( $uid == config('USER_ADMINISTRATOR')){
                return TRUE;
            }
        }
    }
    return FALSE;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装） 
 * @return mixed
 */
function get_client_ip() {
	$ip = '';
	if (isset($_SERVER['HTTP_CDN_SRC_IP']) && $_SERVER['HTTP_CDN_SRC_IP']) {
		$ip = $_SERVER['HTTP_CDN_SRC_IP'];
	} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
		$allIps = $_SERVER['HTTP_X_FORWARDED_FOR'];
		$allIpsArray = explode(',', $allIps);
		$ip = $allIpsArray[0];
	} else if (isset($_SERVER['HTTP_X_REAL_IP']) && $_SERVER['HTTP_X_REAL_IP']) {
		$ip = $_SERVER['HTTP_X_REAL_IP'];
	} else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	if (empty($ip)) {
		$ip = '0.0.0.0';
	}
	return $ip;
}

/**
 *  
 *  指定某列为下标作为新数组
 *  
 */
if (!function_exists('array_column')) { 
    function array_column($array, $val, $key = null){
        $newArr = array();
        if( is_null($key) ){
            foreach ($array as $index => $item) {
                $newArr[] = $item[$val];
            }
        }else{
            foreach ($array as $index => $item) {
                $newArr[$item[$key]] = $item[$val];
            }
        }
        return $newArr;
    }
}

/**
 * 
 *  windows系统信息
 *  
 */
function windows_sysytem_info() {
	$data = array();
	$data['system_info'] = windows_system_info();
	$data['system_config'] = windows_system_config();
	$data['run_time'] = windows_run_time();
	$data['disk_info'] = windows_disk_info();
	$data['physical_memory'] = windows_physical_memory_info();
	$data['virtual_memory'] = windows_virtual_memory_info();
	$data['network_card'] = windows_network_card_info();
	$data['netstat'] = windows_netstat_info();
	$data['window_info'] = windows_info();
	return $data;	
}

	/**
 * 
 * 系统名称、版本和类型
 * 
 */
function windows_system_info()
{
	$out = '';
	$info = exec('wmic os get Caption,Version,OSArchitecture',$out,$status);
	$osinfo_array = explode('  ',$out[1]);
	$osinfo = array_values(array_filter($osinfo_array)); 
//	$str = "系统名称： " . iconv('GBK', 'utf-8', $osinfo[0]) ." | 系统版本： " . $osinfo[2] ." | 系统类型： " . $osinfo[1];
	$arr = array('one'=>'系统名称：','two'=>GBK2UTF8($osinfo[0]),'three'=>'系统版本：','four'=>$osinfo[2],'five'=>'系统类型：','six'=>$osinfo[1]);
	return $arr;

}

/**
 * 
 * 系统配置
 * 
 */
function windows_system_config()
{
	$out = '';
	$info = exec('wmic os get producttype',$out,$status);   #返回 3 是server ,返回其它的是 workstation
	if($out[1] == 3) $osconfig = "Server";
	else $osconfig = "Workstation";
	return "系统配置：" . $osconfig;	
}

/**
 * 
 * 运行时长
 * 
 */
function windows_run_time() {
	$out = '';
	$info = exec('wmic os get lastBootUpTime,LocalDateTime',$out,$status);
	$datetime_array = explode('.',$out[1]);
	$LastBootUpTime = explode(' ',$datetime_array[0]);
	$LocalDateTime = explode(' ',$datetime_array[1]);
	
	$lastbootuptime = $LastBootUpTime['0'];
	$localdatetime = $LocalDateTime['2'];
	
	$uptime = strtotime($localdatetime) - strtotime($lastbootuptime);
	$day=floor(($uptime)/86400);
	$hour=floor(($uptime)%86400/3600);
	$minute=floor(($uptime)%86400/60);
	$second=floor(($uptime)%86400%60);

	return "已运行： ".$day."天".$hour."小时".$minute."分钟".$second."秒";		
}

/**
 * 
 * 硬盘信息
 * 
 */
function windows_disk_info() {
	$out = '';
	$info = exec('wmic logicaldisk get FreeSpace,size /format: list',$out,$status);
	$hd = '';
	foreach($out as $vaule){
		$hd .= $vaule . ' ';;
	}
	$hd_array = explode('   ', trim($hd));
	$key = 'CDEFGHIJKLMNOPQRSTUVWXYZ';
	foreach($hd_array as $k => $v){
		$s_array = explode('Size=', $v);
		$fs_array = explode('FreeSpace=', $s_array[0]);
		$size = round(trim($s_array[1])/(1024*1024*1024), 2);
		$freespace = round(trim($fs_array[1])/(1024*1024*1024), 2);
		$drive = $key[$k];
//			$str[] = $drive . "盘： 已用空间/总空间： " . ($size - $freespace) . "GB/" . $size . "GB | 可用空间： " . $freespace . "GB";
//		$str[] = $drive ."盘 已用空间： ".($size - $freespace)."CB | 总空间： ".$size."GB | 可用空间： ".$freespace."GB";
		$str[] = array('name'=>$drive.'盘','data'=>array('one'=>'已用空间：','two'=>($size - $freespace)."GB",'three'=>'总空间：','four'=>$size."GB",'five'=>'可用空间：','six'=>$freespace."GB"));
	}	
	return $str;
}

/**
 * 
 *  物理内存
 */
function windows_physical_memory_info() {
	$out = '';
	$info = exec('wmic os get TotalVisibleMemorySize,FreePhysicalMemory',$out,$status);
	# 多个空格转为一个空格
	$phymem = preg_replace ( "/\s(?=\s)/","\\1",$out[1]);
	$phymem_array = explode(' ',$phymem);
	$freephymem = round($phymem_array[0]/1024/1024, 2);
	$totalphymem = round($phymem_array[1]/1024/1024, 2);
//		return "已用物理内存/总物理内存： ". ($totalphymem - $freephymem) ."GB/". $totalphymem . "GB | 空闲物理内存： " . $freephymem . "GB";	
//	return '已用物理内存： '.($totalphymem - $freephymem) ."GB | 总物理内存：". $totalphymem . "GB | 空闲物理内存： " . $freephymem . "GB";
	return array('one'=>'已用物理内存：','two'=>($totalphymem - $freephymem) ."GB",'three'=>'总物理内存：','four'=>$totalphymem . "GB",'five'=>'空闲物理内存：','six'=>$freephymem . "GB");
}

/**
 * 
 * 虚拟内存
 * 
 */
function windows_virtual_memory_info() {
	$out = '';
	$info = exec('wmic os get SizeStoredInPagingFiles,FreeSpaceInPagingFiles',$out,$status);
	$pagemem = preg_replace ( "/\s(?=\s)/","\\1",$out[1]);
	$pagemem_array = explode(' ',$pagemem);
	$freepagemem = round($pagemem_array[0]/1024/1024, 2);
	$totalpagemem = round($pagemem_array[1]/1024/1024, 2);
//		return "已用虚拟内存/总虚拟内存： ". ($totalpagemem - $freepagemem) ."MB/". $totalpagemem . "MB | 空闲虚拟内存： " . $freepagemem . "MB";	
//	return "已用虚拟内存： ".($totalpagemem - $freepagemem) ."GB | 总虚拟内存： ".$totalpagemem."GB | 空闲虚拟内存： ". $freepagemem . "GB";	
	return array('one'=>'已用物理内存：','two'=>($totalpagemem - $freepagemem) ."GB",'three'=>'总物理内存：','four'=>$totalpagemem . "GB",'five'=>'空闲物理内存：','six'=>$freepagemem . "GB");
}

/**
 * 
 *  网卡名称
 */
function windows_network_card_info() {
	$out = '';
	$info = exec('wmic nic list brief',$out,$status);
	$nic_array = explode('  ', $out[2], 2);
	$nic = $nic_array[0];
	return GBK2UTF8($nic);
}

/**
 * 
 *  网卡流量
 *  
 */
function windows_netstat_info() {
	# 网卡流量，最初计量为字节
	$out = '';
	$info = exec('netstat -e',$out,$status);
	$out_array = array();
	foreach ($out as $key => $value) {
		$out_array[$key] = mb_convert_encoding ($value, 'utf-8', 'GBK');
	}
	$net = preg_replace ( "/\s(?=\s)/","\\1",$out_array[4]);
	$net_array = explode(' ',$net);
//	return "当前数据流量： 已接收： " .round($net_array[1]/(1024*1024*1024), 2) . "GB | 已发送： " . round($net_array[2]/(1024*1024*1024), 2) . "GB";		
	return array('code'=>'当前数据流量：','data'=>array('one'=>'已接收：','two'=>round($net_array[1]/(1024*1024*1024), 2)."GB",'three'=>'已发送：','four'=>round($net_array[2]/(1024*1024*1024), 2)."GB"));
}

/**
 * 
 * windows电脑信息
 * 
 */
function windows_info () {
	#操作系统所有信息
	$out = '';
	$info = exec('wmic os get /all  /format: list',$out,$status);		
	$out = GBK2UTF8($out);
//		debug($out);
	 
	# 电脑信息
	$out2 = '';
	$info = exec('systeminfo',$out2,$status);
	$out2 = GBK2UTF8($out2);
//		debug($out2);
	#IP配置信息
	$out3 = '';
	$info = exec('ipconfig',$out3,$status);
	$out3 = GBK2UTF8($out3);
//		debug($out3);	

	return array('out'=>$out,'out2'=>$out2,'out3'=>$out3);
}

/**
 *  linux系统信息
 *  
 */
function linux_system_info() {
	//查看linux位数
	$os_num = system("uname -r", $retval); 
	//查看linux版本
	$os_version = system("cat /etc/redhat-release", $retval);
	//实时查看cup和内存使用量
	$memory_status = $this->get_used_status();
	//查看磁盘信息
	$disk_info = system("dh -h", $retval);
	//查看物理CPU个数
	$phy_cpu_nums = system('cat /PRoc/cpuinfo| grep "physical id"| sort| uniq| wc -l', $retval);
	//查看每个物理CPU核数
	$phy_cpu_num = system('cat /proc/cpuinfo| grep "cpu cores"| uniq', $retval);
	//查看逻辑CPU个数
	$logic_cpu_nums = system('cat /proc/cpuinfo| grep "processor"| wc -l', $retval);
	//查看CPU信息（型号）
	$cpu_info_model = system('cat /proc/cpuinfo | grep name | cut -f2 -d: | uniq -c	', $retval);
	return array(
		'os_num'=>$os_num,
		'os_version'=>$os_version,
		'memory_status'=>$memory_status,
		'disk_info'=>$disk_info,
		'phy_cpu_nums'=>$phy_cpu_nums,
		'phy_cpu_num'=>$phy_cpu_num,
		'logic_cpu_nums'=>$logic_cpu_nums,
		'cpu_info_model'=>$logic_cpu_nums
	);
}

/**
 * 
 * 实时查看cup和内存使用量
 *
 */
function get_used_status(){
	$fp = popen('top -b -n 2 | grep -E "^(Cpu|Mem|Tasks)"',"r");//获取某一时刻系统cpu和内存使用情况
	$rs = "";
	while(!feof($fp)){
		$rs .= fread($fp,1024);
	}
	pclose($fp);
	$sys_info = explode("\n",$rs);
	$tast_info = explode(",",$sys_info[3]);//进程 数组
	$cpu_info = explode(",",$sys_info[4]);  //CPU占有量  数组
	$mem_info = explode(",",$sys_info[5]); //内存占有量 数组
	//正在运行的进程数
	$tast_running = trim(trim($tast_info[1],'running'));

	//CPU占有量
	$cpu_usage = trim(trim($cpu_info[0],'Cpu(s): '),'%us');  //百分比

	//内存占有量
	$mem_total = trim(trim($mem_info[0],'Mem: '),'k total'); 
	$mem_used = trim($mem_info[1],'k used');
	$mem_usage = round(100*intval($mem_used)/intval($mem_total),2);  //百分比

	$fp = popen('df -lh | grep -E "^(/)"',"r");
	$rs = fread($fp,1024);
	pclose($fp);
	$rs = preg_replace("/\s{2,}/",' ',$rs);  //把多个空格换成 “_”
	$hd = explode(" ",$rs);
	$hd_avail = trim($hd[3],'G'); //磁盘可用空间大小 单位G
	$hd_usage = trim($hd[4],'%'); //挂载点 百分比 磁盘已用空间大小

	return array('cpu_usage'=>$cpu_usage,'mem_usage'=>$mem_usage,'hd_avail'=>$hd_avail,'hd_usage'=>$hd_usage,'tast_running'=>$tast_running,);
}

/**
 * 
 * 将GBK转为UTF-8
 * @param unknown_type $arr
 * 
 */
function GBK2UTF8($arr) {
	$arrary = array();
	
	if (is_array($arr)) {
		foreach ($arr as $k=>$value) { 
			if ($value) {
				if (is_array($value)) {
					$arrary[$k] = GBK2UTF8($value);
				} else {
					$arrary[$k] = iconv('GBK', 'utf-8', $value);
				}					
			}
		}
	} else {
		$arrary = iconv('GBK', 'utf-8', $arr);
	}
	return $arrary;
}