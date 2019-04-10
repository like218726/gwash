<?php

namespace app\admin\controller;

use app\common\SystemEmail;

use PHPMailer\PHPMailer\PHPMailer;

use PHPMailer\PHPMailer\Exception;

class System extends Base
{
	
	/**
	 * 
	 * 列表
	 * 
	 */
    public function index()
    {
    	if ($this->request->isGet()) {
    		$config = model('SystemConfig')->find()->toArray(); 
        	$this->assign('config',$config);
        	return $this->fetch();
    	}
    }

    /**
     * 
     * 保存
     * 
     */
    public function save()
    {
    	if ( $this->request->isPost() ) {
	        $config = $this->request->post(); 
	        unset($config['file']);
	        $config['site_logo'] = trim($config['site_logo']);
	        $config['site_icon'] = trim($config['site_icon']); 
	        if (empty($config['id'])){
	            $res = model('SystemConfig')->add($config);
	        } else {
	            $res = model('SystemConfig')->update($config,array('site_url'=>$config['site_url']));
	        }
	
	        if ($res === false){
	        	return ajaxError('保存失败');
	        }
	        return ajaxSuccess('保存成功');    		
    	}
    }

    /**
     * 
     * 邮件
     * 
     */
    public function email() 
    {
    	if (session('nick_name') != 'admin') {
    	    if ( $this->request->isGet() ) {
	    		$config = model('SystemEmail')->find();
    			$this->assign('is_ssl', SystemEmail::$_system_email['is_ssl']); 
    			$this->assign('is_auth', SystemEmail::$_system_email['is_auth']); 
        		$this->assign('config',$config);
    			return $this->fetch("email_detail");
	    	}    		
    	} else {
    		if ( $this->request->isGet() ) {
    			$config = model('SystemEmail')->find();
    			$this->assign('is_ssl', SystemEmail::$_system_email['is_ssl']); 
    			$this->assign('is_auth', SystemEmail::$_system_email['is_auth']); 
        		$this->assign('config',$config);
    			return $this->fetch();
    		} else {
    			$id = input('id', '0', 'trim');
    			$sender = input('post.sender', '', 'trim');
    			$server = input('post.server', '', 'trim');
    			$receiver = input('post.receiver', '', 'trim');
    			$receiver_name = input('post.receiver_name', '', 'trim');
    			$is_ssl = input('post.is_ssl', '0', 'trim');
    			$port = input('post.port', '0', 'trim');
    			$ssl_port = input('post.ssl_port', '0', 'trim');
    			$is_auth = input('post.is_auth', '0', 'trim');
    			$password = input('post.password', '', 'trim');
    			$auth_code = input('post.auth_code', '', 'trim');
    			
    			if ($receiver_name != '') {
    				if (count(explode(",", $receiver_name)) != count(explode(",", $receiver))) {
    					return ajaxError("收件人名称的数量与收件人的数量不一致,请重新填写");
    				}
    			}
    			
    			if (!$port && !$ssl_port) {
    				return ajaxError("端口或者加密端口必填");
    			}
    			
    			if (!$password && !$auth_code) {
    				return ajaxError("密码或者授权码必填");
    			}
    			
    			if ($is_ssl == 1 && !$ssl_port) {
    				return ajaxError("加密端口必填");
    			}
    			
    			if ($is_ssl == 0 && !$port) {
    				return ajaxError("端口必填");
    			}
    			
    			if ($is_auth == 1 && !$auth_code) {
    				return ajaxError("授权码必填");
    			}
    			
    			if ($is_auth == 0 && !$password) {
    				return ajaxError("密码必填");
    			}
    			
    			$data = [];
    			$data['id'] = $id;
    			$data['sender'] = $sender;
    			$data['server'] = $server;
    			$data['receiver_name'] = $receiver_name;
    			$data['receiver'] = $receiver;
    			$data['is_ssl'] = $is_ssl;
    			$data['port'] = $port;
    			$data['ssl_port'] = $ssl_port;
    			$data['is_auth'] = $is_auth;
    			$data['password'] = $password;
    			$data['auth_code'] = $auth_code;
    			$data['create_time'] = date('Y-m-d H:i:s', time()); 
    			if ($data['id']) {
    				$res = model('SystemEmail')->where(['id'=>$id])->update($data); 
    			} else {
    				$res = model('SystemEmail')->insert($data);
    			} 
    			if ($res === false) {
    				return ajaxError("操作失败");
    			} else {
    				return ajaxSuccess("操作成功");
    			}
    		}
    	}
    }
    
    /**
     * 
     * 测试邮件发送
     */
    public function send() {
    	if ($this->request->isPost()) { 
    		$res = model('SystemEmail')->find();
    		$sender = $res['sender'];
    		$is_auth = $res['is_auth'];
    		if ($res['is_auth'] == 1) {
    			$password = $res['auth_code'];
    		} else {
    			$password = $res['password'];
    		}
    		$server = $res['server'];
    		$receiver = explode(",", $res['receiver']);
    		$receiver_name = explode(",", $res['receiver_name']);
    		if ($res['is_ssl'] == 1) {
    			$port = $res['ssl_port'];
    		} else {
    			$port = $res['port'];
    		}
    		$is_ssl = $res['is_ssl'];
    		$Subject = 'Test mail.';
    		$body = 'Hello, this is a test mail using phpmailer';
    		$Altbody = 'Hello, this is a test mail using phpmailer';
    		$Attachment = [
	    		'0'=>[
	    			'attachment_file'=>ROOT_PATH.'/public/Uploads/20190404/8fa36a137f32e42f80932d935e8fca9b.png',
	    			'attachment_name'=>'悦动羽毛球馆',
	    		],
	    		'1'=>[
	    			'attachment_file'=>ROOT_PATH.'/public/Uploads/20181210/c60a798cc64f9f675b79402c9db09817.jpg',
	    			'attachment_name'=>'beautifully gril',
	    		],
    		];
     		$email = send_email($sender, $server, $receiver, $receiver_name, $is_auth, $password, $is_ssl, $port, $Subject, $body, $Altbody, $Attachment);
    		if ($email['code'] == 200) {
    			return ajaxSuccess("操作成功");
    		} else {
    			return ajaxError("操作失败".$email['msg']);
    		}
     		
    	}
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