<?php
namespace app\admin\controller;

use think\Controller;

class Verification extends Controller {

    private $gt_captcha_id = 'YourID';
    private $gt_private_key = 'YourKey';

    public function gt(){
        $rnd1           = md5(rand(0, 100));
        $rnd2           = md5(rand(0, 100));
        $challenge      = $rnd1 . substr($rnd2, 0, 2);
        $result         = array(
            'success'   => 0,
            'gt'        => $this->gt_captcha_id,
            'challenge' => $challenge,
            'new_captcha'=>1
        );
        return json($result);
    }
}