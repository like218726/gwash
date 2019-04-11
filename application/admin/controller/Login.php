<?php

namespace app\admin\controller;

use think\Db;

use think\Loader;

class Login extends Base {
	
	/**
	 * 
	 * 登陆入口
	 * 
	 */
	public function index() {
		if ($this->request->isGet()) {
			return $this->fetch();
		}
	}
	
	/**
	 * 
	 * 用户登陆
	 * 
	 */
    public function login() {
    	if ($this->request->isPost()) {
    	    $pass = md5(input('post.password','','trim'));
	        $user = input('post.username','','trim');
	
	        $challenge = input('post.geetest_challenge','','trim');
	        $validate = input('post.geetest_validate','','trim');
	        if(!$challenge || md5($challenge) != $validate){
	            return ajaxError('请先通过验证！');
	        }
	
	        $userInfo = model('SystemAdminUser')->where(['username' => $user, 'password' => $pass])->find();
	
	        if (!empty($userInfo)) {
	            if ($userInfo['status'] == 1) {
	
	                //保存用户信息和登录凭证
	                cache($userInfo['id'], session_id(), config('ONLINE_TIME'));
	                session('uid', $userInfo['id']);
	                session('nick_name', $userInfo['nickname']);
	                session('real_name', $userInfo['realname']);

	                $ip = $this->request->ip();
	                
	                //更新用户数据
	                $userData = model('SystemAdminUserData')->where(['uid' => $userInfo['id']])->find();
	                $data = array();	                
                    
                    $data['lastLoginIp'] = $ip;
                    $data['lastLoginTime'] = time();
 
	                if ($userData) {
	                	$data['loginTimes'] = $userData['loginTimes'] + 1;	                    
	                } else {
	                    $data['uid'] = $userInfo['id'];
	                    $data['loginTimes'] = 1;
	                }
	                Loader::import('ip.IpLocation', EXTEND_PATH, '.class.php');  
		    		$iplocation = new \IpLocation();
		    		$ipinfo = $iplocation->getlocation($ip); // 获取域名服务器所在的位置           
	                $admin_user_data['last_ip'] = get_client_ip();
	                $admin_user_data['last_login'] = time();
	                $admin_user_data['last_city'] = GBK2UTF8($ipinfo['country']);
	                $admin_user_data['login_count'] = $userInfo['login_count'] + 1;
	                
	                Db::startTrans();
	                try {
	                	if ($userData) {
	                		model('SystemAdminUserData')->save($data, ['uid' => $userInfo['id']]);
	                	} else {
	                		model('SystemAdminUserData')->save($data);
	                	}
	                	model('SystemAdminUser')->save($admin_user_data,['id' => $userInfo['id']]);	
	                	Db::commit();
	                	return ajaxSuccess('登录成功');
	                } catch (\Exception $e) {
	                	Db::rollback();
	                	return ajaxError('登录失败:'.$e->getMessage());
	                }
	            } else {
	                return ajaxError('用户已被封禁，请联系管理员');
	            }
	        } else {
	            return ajaxError('用户名密码不正确');
	        }    		
    	}

    }	
    
    /**
     * 
     * 退出
     * 
     */
    public function logOut() {
    	if ($this->request->isGet()) {
    		cache(session('uid'), null);
	        session('[destroy]');
	        $this->success('退出成功', 'login/index');
    	}
    }    
    
    /**
     * 
     * 修改用户信息
     * 
     */
    public function changeUser() {
        if ($this->request->isPost()) {
            $data = input('post.');
            $newData = array();
            if (!empty($data['nickname'])) {
                $newData['nickname'] = $data['nickname'];
            }
        	if (!empty($data['realname'])) {
                $newData['realname'] = $data['realname'];
            }
        	if (!empty($data['mobile'])) {
                $newData['mobile'] = $data['mobile'];
            }
            if (!empty($data['password'])) {
                $newData['password'] = md5($data['password']);
                $newData['updateTime'] = time();
            }
            $res = model('SystemAdminUser')->where(['id' => session('uid')])->update($newData);

            if ($res === false) {
                return ajaxError('修改失败');
            } else {
                return ajaxSuccess('修改成功');
            }
        } else { 
            $userInfo = model('SystemAdminUser')->where(['id' => session('uid')])->find(); 
            return $this->fetch("changeUser",["uname"=>$userInfo['username'], "userinfo"=>$userInfo]);
        }    	
    } 

    /**
     * 
     * 权限提示页
     * 
     */
    public function ruleTip(){
    	if ($this->request->isGet()) {
    		return view("login/ruleTip");
    	}
    }
    
}