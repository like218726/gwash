<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Base extends Controller {

    protected $userInfo;
    protected $allMenu;
    protected $uid;

    private $url;
    private $menuInfo;

    public function _initialize(){

        //初始化系统
        $this->uid = session('uid');
        $this->assign('uid', $this->uid);
        $this->iniSystem();

        //控制器初始化
        if(method_exists($this, 'myInit')){
            $this->myInit();
        }

        // 读取数据库中读取参数字典
        $config_dict = cache('DB_CONFIG_DICT_DATA');
        if (!$config_dict) {
            $config_dict = model('SystemConfigDict')->getDictLists();
            cache('DB_CONFIG_DICT_DATA', $config_dict);
        }
        // 添加配置
        cache($config_dict);

        $site_url = ('SITE_URL');
        $this->assign('site_url',$site_url);

    }

    /**
     * 自定义初始化函数
     */
    public function myInit(){}    

    /**
     * 将二维数组变成指定key
     * @param $array
     * @param $keyName
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    protected function buildArrByNewKey($array, $keyName = 'id') {
        $list = array();
        foreach ($array as $item) {
            $list[$item[$keyName]] = $item;
        }
        return $list;
    }

    /**
     * 
     * 系统初始化
     * 
     */
    private function iniSystem() { 
        $request = Request::instance();
        $this->url = $request->controller() .'/' . $request->action();  
//        $this->url2 = $request->path();  // tp获取控制器路由的方法
        $this->isForbid();
        if ($request->controller() != 'Login') { 
            $this->allMenu = model('SystemMenu')->order('sort desc')->select();
            $this->allMenu = $this->allMenu->toArray();
            $this->menuInfo = model('SystemMenu')->where(array('url' => $this->url))->find();
           
            if (empty($this->menuInfo)) {
                if ($request->isAjax()) {
                    ajaxError('当前URL非法')->send();
                    exit;
                } else {
                	return ajaxError('当前URL非法2');
                }
            }  
            $this->checkLogin();
            $this->checkRule();
            $this->iniLog();
        }
    }

    /**
     * 封号，或者封IP等特殊需求才用到的
     * @return bool
     */
    private function isForbid() {
        return true;
    }

    /**
     * 检测登录
     */
    private function checkLogin() { 
        if (isset($this->uid) && !empty($this->uid)) {
            $sidNow = session_id();
            $sidOld = cache($this->uid); 

            if (! cache($this->uid)) {
            	$this->error("您在".(config('ONLINE_TIME')/3600)."小时内 长时间未操作或相同账号登录,请重新登录！", url('Login/index'));
            }   
            
            if (isset($sidOld) && !empty($sidOld)) {
                if ($sidOld !== $sidNow) {
                    $this->error("您的账号在别的地方登录了，请重新登录！", url('Login/index'));
                } else {
                    cache($this->uid, $sidNow, config('ONLINE_TIME'));
                    $this->userInfo = $userInfo = model('SystemAdminUser')->where(array('id' => $this->uid))->find();
                    $this->assign('userInfo', $this->userInfo);
                }
            } else {
                $this->error("登录超时，请重新登录！", 'Login/index');
            }
        } else {
            $this->redirect('login/index');
        }

    }

    /**
     * 检测权限
     */
    private function checkRule() {
        $isAdmin = isAdministrator();
        if ($isAdmin) {
            return true;
        } else {
            $authObj = new Auth();
            $check = $authObj->check(strtolower($this->url), $this->uid);
            if (!$check) {
                //修改没有权限时的跳转页面
                if (strtolower($this->url) == 'index/index'){ //登录时没有首页的权限直接返回登录页面
                    $this->error(lang('没有权限'), 'Login/index');
                } else {
                    $request = Request::instance();
                    if($request->isAjax()){
                        ajaxError(lang('没有权限'))->send();
                        exit;
                    }else{
                        $this->error(lang('没有权限'), 'Login/ruleTip');
                    }

                }
            }
        }
    }    

    /**
     * 根据菜单级别进行区别Log记录，当然，如果有更加细节的控制，也可以在这个函数内实现
     */
    private function iniLog() {
        $data = array(
            'actionName' => $this->menuInfo['name'],
            'uid' => $this->uid,
            'nickname' => $this->userInfo['nickname'],
            'addTime' => time(),
            'url' => $this->menuInfo['url'],
            'data' => json_encode($_REQUEST)
        );
        model('SystemAdminUserAction')->save($data);
    }
   
}