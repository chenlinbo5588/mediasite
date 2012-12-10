<?php
/**
 *公共Action
 *
 */
class CommonAction extends Action {
    public function _initialize(){
        
    }

    public function login($account, $password) {
        $retAry = array('status' => false);
        $userModel = M('User');
        $con['account'] = $account;
        $userMsg   = $userModel->where($con)->select();
        var_dump($userMsg);
    }

    /**
    *
    *重定向页面
    */
    public function directTo($url = '') {
        if($url != '') {
            $script = "<script>window.top.location.href='".$url."'</script>";
            die($script);
        }
    }

    /**
    *
    *返回json
    */
    public function sendJson($data, $isJsonHead = true) {
        if($isJsonHead)
            header("Content-Type:application/json; charset=utf-8");
         exit(json_encode($data));
    }
}