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
            $retAry['error'] = L('LOGIN_MSG_NOT_FULL_ERR');
        }
        if($retAry['status']) {
            redirect(__APP__ . '/Index');
        }
        redirect(__APP__ . '/Login/index');
    }

    public function logout() {
        Session::set("User", '');
        session_destroy();
        redirect(__APP__ . '/Login/index');
    }
}