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
        $url = __APP__ . '/Login/index';
        if($retAry['status']) {
            $userType = $this->_user['Type'];
            switch($userType) {
                case '0':
                    $url = __APP__ . '/Upload';
                    break;
                case '1':
                    $url = __APP__ . '/Admin/client';
                    break;
                default:
                    $url = __APP__ . '/Index';
                    break;
            }
        }
        redirect($url);
    }

    public function logout() {
        Session::set("User", '');
        session_destroy();
        redirect(__APP__ . '/Login/index');
    }
}