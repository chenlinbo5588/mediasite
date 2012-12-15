<?php
/**
 *public Action
 *
 */
import("ORG.Util.Session");
class CommonAction extends Action {
    protected $_user        = array();
    public function _initialize(){
        $this->_user    = Session::get('User');
        if(!$this->_user['IsLogon']) {
            Session::set('User','');
        }
        $userType = 9999;
        $isLogon  = $this->_user['IsLogon'];
        $nickName = $this->_user['NickName'];
        $account  = $this->_user['Account'];
        $userType = $this->_user['Type'];
        $this->assign('isLogon',$isLogon);
        $this->assign('nickName',$nickName);
        $this->assign('userType',$userType);
    }

    /**
    *user login
    *
    *@param string $account //user login account
    *@param string $password //user login password
    *@return array $retAry  //array('status' => bool,'error' => ''),status of login and error message
    */
    public function login($account, $password) {
        $retAry = array('status' => false);
        $userModel = M('User');
        $con['account'] = $account;
        $con['enable'] = 1;
        $userReg   = $userModel->where($con)->limit(0,1)->select();
        if(isset($userReg[0])) {
            $userMsg = $userReg[0];
            if($password == $userMsg['password']) {
                Session::set('User', array(
                             'IsLogon'       => true,                   // login flag
                             'ID'            => $userMsg['id'],         // user id
                             'Account'       => $userMsg['account'],    // account
                             'NickName'      => $userMsg['nickname'],   // nickname
                             'Type'          => $userMsg['type'],       //user type
                             'Remember'      => false                   //default
                         ));
                $retAry['status'] = true;
            } else {
                $retAry['error'] = L('LOGIN_USER_PWD_ERR');
            }
        } else {
            $retAry['error'] = L('LOGIN_NO_USER_ERR');
        }
        return $retAry;
    }

    /**
    *direct to page
    *@param string $url //target url
    *
    */
    public function directTo($url = '') {
        if($url != '') {
            $script = "<script>window.top.location.href='".$url."'</script>";
            die($script);
        }
    }

    /**
    *
    *return json data
    *@param array $data 
    *@param bool $jsJsonHead
    */
    public function sendJson($data, $isJsonHead = true) {
        if($isJsonHead)
            header("Content-Type:application/json; charset=utf-8");
         exit(json_encode($data));
    }
}