<?php
class LoginAction extends CommonAction {
    public function index(){
        $this->display();
    }

    public function check() {
        $retAry     = array('status' => false);
        $account    = trim($_POST['account']);
        $password   = trim($_POST['password']);
        if($account && $password) {
            $retAry = $this->login($account, $password);
        }
        else {
            $retAry['error'] = 'Login info is null';
            //Session::set("User", '');
        }
        if($retAry['status']) {
            $this->redirect('Index/index',1);
        }
    }
}