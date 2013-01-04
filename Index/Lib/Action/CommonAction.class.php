<?php
/**
 *public Action
 *
 */
import("@.ORG.Util.Session");
import("@.ORG.Util.Page");
import("@.ORG.Crypt.Rsa");
class CommonAction extends Action {
    protected $_user        = array();
    public function _initialize(){
        $this->_user    = Session::get('User');
        $userType = 9999;
        $isLogon  = $this->_user['IsLogon'];
        $nickName = $this->_user['NickName'];
        $account  = $this->_user['Account'];
        $userType = $this->_user['Type'];
        $modName      = strtolower(MODULE_NAME);
        $actionName   = strtolower(ACTION_NAME);
        $loginMod     = array('admin');
        $adminAction = array('client','product','project','folder');
        if(!$this->_user['IsLogon'] && in_array($modName,$loginMod)) {
            Session::set('User','');
            $script = "<script>window.top.location.href='".__APP__."/Login'</script>";
            die($script);
        }
        if(($userType != 1) && in_array($actionName,$adminAction)) {
            $script = "<script>window.top.location.href='".__APP__."/Index'</script>";
            die($script);
        }
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
                $this->_user    = Session::get('User');
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
    
    
    /**
     *
     * @param type $respCode
     * @param type $body
     * @param type $redirectUrl
     * @param type $isJsonHead 
     */
    public function sendFormatJson($respCode,$body,$redirectUrl = '',$isJsonHead = true){
	if($isJsonHead)
            header("Content-Type:application/json; charset=utf-8");
	
	$data = array(
	    'code' => $respCode,
	    'body' => $body
	);
	
	//重定向URL
	if('' != $redirectUrl){
	    $data['redirectUrl'] = $redirectUrl;
	}
	
	exit(json_encode($data));
    }

    /*
    *
    *show page
    *
    */
    public function showPage($pageObj,$search='') {
        $pageObj->parameter = ($search != '') ? "search={$search}" : '';
        $pageObj->setConfig('header', '<div class="pager">');
        $pageObj->setConfig('end', '</div>');
        $pageObj->setConfig('first', '<span class="first">&lt;&lt;</span>');
        $pageObj->setConfig('last', '<span class="last">&gt;&gt;</span>');
        $pageObj->setConfig('prev', '<span class="prev">PREV</span>');
        $pageObj->setConfig('next', '<span class="next">NEXT</span>');
        $pageObj->setConfig('theme', ' %header% %first% %upPage% %prePage% %linkPage% %nextPage% %downPage% %end%');
        $page = $pageObj->show();
        return $page;
    }

    /**
    *
    *add data
    *@param mix $modelName 
    *@param array $data
    *@return array $retAry //array('status' => true,'insertid' => 1);
    *                      //array('status' => false,'error' => 'error message')
    */
    protected function _add($modelName,$data) {
        $retAry = array('status' => false);
        $model = M($modelName);
        $model->create($data);
        $error = $model->getError();
        if(!empty($error)) {
            $retAry['error'] = $error;
        }
        else {
            $insertid 	= $model->add();
            $error	 	= $model->getError();
            if(empty($error) && $insertid > 0) {
                $retAry['status'] = true;
                $retAry['insertid'] = $insertid;
            }
            else {
                $retAry['error'] = $error;
            }
        }
        return $retAry;
    }

    /**
    *
    *delete data
    *@param mix $modelName 
    *@param array/string $con
    *@return array $retAry //array('status' => true);
    *                      //array('status' => false,'error' => 'error message')
    */
    protected function _delete($modelName, $con) {
        $retAry = array('status' => false);
        if(empty($con)) return $retAry;
        if(is_numeric($con)) {
            $where = "id='{$con}'";
        }
        else {
            $where = $condition;
        }
        $model = M($modelName);
        $flag = $model->where($where)->delete();
        if($flag) $retAry['status'] = $flag;
        return $retAry;
    }

    /**
    *
    *update data
    *@param mix $modelName 
    *@param array/string $con
    *@param array $data
    *@return array $retAry //array('status' => true,'affected' => true);
    *                      //array('status' => true,'affected' => false);
    *                      //array('status' => false,'error' => 'error message')
    */
    protected function _update($modelName, $con, $data) {
        $retAry 	= array('status' => false);
        if(empty($con)) {
            $ret['error'] = 'condition is null';
            return $retAry;
        }

        $flag   = false;
        $model 	= D($modelName);

        if(is_numeric($con)) {
            $where = "id='{$con}'";
        }
        else {
            $where = $con;
        }
        $flag = $model->where($where)->save($data);

        $error = $model->getError();
        if(!$error && $flag == true) { 
            $retAry['status']      = true;
            $retAry['affected']    = true;
        }
        else if(!$error && $flag == false) { 
            $retAry['status']      = true;
            $retAry['affected']    = false;
        }
        else {
            $retAry['error']       = $error;
        }
        return $retAry;
    }

    /*
     *加密信息
    */
    protected function encodeInfo($str) {
		$rsa = new Rsa();//加密数据
		$pub = $rsa->hex2dec(C('SYSTEM_RSA_PUB'));
		$mod = $rsa->hex2dec(C('SYSTEM_RSA_MOD'));
		$encodeStr = $rsa->rsa_encode($str,$pub,$mod);
		return $encodeStr;
    }

    /*
     *解密信息
    */
    protected function decodeInfo($str) {
        $rsa = new Rsa();//解密数据
        $pri = $rsa->hex2dec(C('SYSTEM_RSA_PRI'));
        $mod = $rsa->hex2dec(C('SYSTEM_RSA_MOD'));
        $decodeStr = $rsa->rsa_decode($str,$pri,$mod);
        return $decodeStr;
    }
}