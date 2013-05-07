<?php
class AdminAction extends CommonAction {
    public function index(){
        if($this->_user['Type'] == 1) {
            redirect(__URL__ . '/user');
        } else {
            redirect(__APP__ . '/Index');
        }
    }

    public function user(){
        $search = $_POST['search'];
        $model = M('User');
        $page     = 1;
        $pageSize = 10;
        $total    = 0;
        $list = array();
        $con = array();
        if($search != '') {
            $scon = array();
            $scon[] = "id like '%{$search}%'";
            $scon[] = "account like '%{$search}%'";
            $scon[] = "nickname like '%{$search}%'";
            $con[] = '('.implode(' OR ',$scon).')';
        }
        $con[] = 'enable IN (0,1)';
        $where = implode(' AND ',$con);
        $total = $model->where($where)->count();
        $pageObj = new Page($total,$pageSize);
        $list   = $model->where($where)
                        ->limit($pageObj->firstRow. ',' . $pageObj->listRows)
                        ->order('id desc')
                        ->select();
        $page = $this->showPage($pageObj,$search);
        $this->assign('list',$list);
        $this->assign("page", $page);
        $this->display();
    }

    public function editUser() {
        $editId = $_GET['id'] ? $_GET['id'] : 0;
        if($editId) {
            $model = M('User');
            $client = $model->where("id={$editId}")->limit(0,1)->select();
            if(isset($client[0])) {
                $clientMsg = $client[0];
                $this->assign('vo',$clientMsg);
            }
        }
        $this->display();
    }

    public function submitUser() {
        $retAry = array('status' => false);
        if(isset($_POST['id']) && ($_POST['id'] != 0)) {
            $con['id']      = $_POST['id'];
            $data = $_POST;
            unset($data['id']);
            //user type should not be edit
            unset($data['type']);
            $retAry = $this->_update('User',$con,$data);
            if(($data['enable'] == '0') && $retAry['status']) {
                $pcon['enable'] = $data['enable'];
                $pcon['user_id'] = $con['id'];
                $this->_update('Product',$pcon,$data);
                $this->_update('Project',$pcon,$data);
            }
        } else {
            $data  = $_POST;
            $data['enable'] = 1;
            
            if(!in_array($data['type'],array(0,2))){
                //default 0
                $data['type'] = 0;
            }
            
            $data['createtime'] = date('Y-m-d H:i:s');
            $retAry = $this->_add('User',$data);
        }
        $this->sendJson($retAry);
    }

    public function delUser() {
        $retAry = array('status' => false);
        $editId = $_POST['id'];
        $con['id']      = $editId;
        $data['account'] = time();
        $data['enable'] = 2;
        $retAry = $this->_update('User',$con,$data);
        if($retAry['status']) {
            $pcon['user_id'] = $editId;
            $data['name'] = time();
            $this->_update('Product',$pcon,$data);
            $this->_update('Project',$pcon,$data);
        }
        $this->sendJson($retAry);
    }

    public function product(){
        $search = $_POST['search'];
        $model = M('Product');
        $page     = 1;
        $pageSize = 10;
        $total    = 0;
        $list = array();
        if($search != '') {
            $scon = array();
            $scon[] = "product.id like '%{$search}%'";
            $scon[] = "product.name like '%{$search}%'";
            $con[] = '('.implode(' OR ',$scon).')';
        }
        if($_GET['uid'] > 0)$con[] = 'product.user_id IN ('.$_GET['uid'].')';
        $con[] = 'product.enable IN (0,1)';
        $where = implode(' AND ',$con);
        $total = $model->where($where)->count();
        $pageObj = new Page($total,$pageSize);
        $list   = $model->field('product.id AS ID,product.name AS Name,product.enable AS Enable,user.id AS UserID,user.nickname AS Nickname,user.account AS Account')
                        ->join('user ON user.id = product.user_id')
                        ->where($where)
                        ->limit($pageObj->firstRow. ',' . $pageObj->listRows)
                        ->order('product.id desc')
                        ->select();
        $page = $this->showPage($pageObj,$search);
        $this->assign('list',$list);
        $this->assign("page", $page);
        $this->display();
    }

    public function editProduct() {
        $editId  = $_GET['id'] ? $_GET['id'] : 0;
        $editMsg = array();
        if($editId) {
            $model  = M('Product');
            $client = $model->field('product.name AS Name,product.enable AS Enable,user.nickname AS Nickname,user.account AS Account')
                            ->join('user ON user.id = product.user_id')
                            ->where("product.id={$editId}")
                            ->limit(0,1)
                            ->select();
            if(isset($client[0])) {
                $editMsg = $client[0];
                $this->assign('vo',$editMsg);
            }
        }
        $this->assign('vo',$editMsg);
        $this->assign('editId',$editId);
        $userMod = M('User');
        $list   = $userMod->field('id,nickname,account')
                          ->where('enable=1 AND type != 1')
                          ->order('id desc')
                          ->select();
        $this->assign('userList',$list);
        $this->display();
    }

    public function submitProduct() {
        $retAry = array('status' => false);
        if(isset($_POST['id']) && ($_POST['id'] != 0)) {
            $con['id']      = $_POST['id'];
            $data = $_POST;
            unset($data['id']);
            $retAry = $this->_update('Product',$con,$data);
        } else {
            $data  = $_POST;
            $data['enable'] = 1;
            unset($data['id']);
            $retAry = $this->_add('Product',$data);
        }
        $this->sendJson($retAry);
    }

    public function delProduct() {
        $retAry = array('status' => false);
        $editId = $_POST['id'];
        $con['id']      = $editId;
        $data['name'] = time();
        $data['enable'] = 2;
        $retAry = $this->_update('Product',$con,$data);
        if($retAry['status']) {
            $pcon['product_id'] = $editId;
            $this->_update('Project',$pcon,$data);
        }
        $this->sendJson($retAry);
    }

    public function folder(){
        $search = $_POST['search'];
        $model = M('Project');
        $page     = 1;
        $pageSize = 10;
        $total    = 0;
        if($search != '') {
            $scon = array();
            $scon[] = "project.id like '%{$search}%'";
            $scon[] = "project.name like '%{$search}%'";
            $con[] = '('.implode(' OR ',$scon).')';
        }
        if($_GET['pid'] > 0)$con[] = 'project.product_id IN ('.$_GET['pid'].')';
        $con[] = 'project.enable IN (0,1)';
        $where = implode(' AND ',$con);
        $total = $model->where($where)->count();
        $pageObj = new Page($total,$pageSize);
        $list   = $model->field('project.id AS ID,project.name AS Name,project.enable AS Enable,product.name AS ProductName,user.nickname AS Nickname,user.account AS Account')
                            ->join('product ON product.id = project.product_id')
                            ->join('user ON user.id = project.user_id')
                            ->where($where)
                            ->limit($pageObj->firstRow. ',' . $pageObj->listRows)
                            ->order('project.id desc')
                            ->select();
        $page = $this->showPage($pageObj,$search);
        $this->assign('list',$list);
        $this->assign("page", $page);
        $this->display();
    }

    public function editFolder() {
        $editId  = $_GET['id'] ? $_GET['id'] : 0;
        $editMsg = array();
        if($editId) {
            $model  = M('Project');
            $client = $model->field('project.name AS Name,project.enable AS Enable,product.name AS ProductName,user.nickname AS Nickname,user.account AS Account')
                            ->join('product ON product.id = project.product_id')
                            ->join('user ON user.id = project.user_id')
                            ->where("project.id={$editId}")
                            ->limit(0,1)
                            ->select();
            if(isset($client[0])) {
                $editMsg = $client[0];
                $this->assign('vo',$editMsg);
            }
        }
        $this->assign('vo',$editMsg);
        $this->assign('editId',$editId);
        $userMod = M('User');
        $list   = $userMod->field('id,nickname,account')
                          ->where('enable=1 AND type != 1')
                          ->order('id desc')
                          ->select();
        $this->assign('userList',$list);
        $userMod = M('Product');
        $list   = $userMod->field('id,name')
                          ->where('enable=1 AND user_id='.$list[0]['id'])
                          ->order('id desc')
                          ->select();
        $this->assign('productList',$list);
        $this->display();
    }

    public function submitFolder() {
        $retAry = array('status' => false);
        if(isset($_POST['id']) && ($_POST['id'] != '0')) {
            $con['id']      = $_POST['id'];
            $data = $_POST;
            unset($data['id']);
            $retAry = $this->_update('Project',$_POST['id'],$data);
        } else {
            $data  = $_POST;
            $data['enable'] = 1;
            unset($data['id']);
            $retAry = $this->_add('Project',$data);
        }
        if(isset($retAry['error'])) {
            $retAry['error'] = str_replace('project','folder',$retAry['error']);
        }
        $this->sendJson($retAry);
    }

    public function delFolder() {
        $retAry = array('status' => false);
        $editId = $_POST['id'];
        $con['id']      = $editId;
        $data['name'] = time();
        $data['enable'] = 2;
        $retAry = $this->_update('Project',$con,$data);
        $this->sendJson($retAry);
    }
    
    public function file(){
        $userType = $this->_user['Type'];
        $account  = $this->_user['Account'];
        $this->assign("userType", $userType);
        if($userType != 1) {
            $this->cfile();die;
        }
        $search = $_POST['search'];
        $model = M('Files');
        $page     = 1;
        $pageSize = 10;
        $total    = 0;
        $list = array();
        $con = array();
        $con[] = 'is_delete<>1';
        if($userType != 1) {
            $con[]    = "account = '{$account}'";
        }
        if($search != '') {
            $scon = array();
            $scon[] = "title like '%{$search}%'";
            $scon[] = "account like '%{$search}%'";
            $con[] = '('.implode(' OR ',$scon).')';
        }
        $where = implode(' AND ',$con);
        $total = $model->where($where)->count();
        $pageObj = new Page($total,$pageSize);
        $list   = $model->where($where)
                        ->limit($pageObj->firstRow. ',' . $pageObj->listRows)
                        ->order('id desc')
                        ->select();
        $page = $this->showPage($pageObj,$search);
        $this->assign('list',$list);
        $this->assign("page", $page);
        $this->display($tplName);
        
    }

    public function cfile() {
        $userID = $this->_user['ID'];
        $page     = 1;
        $pageSize = 10;
        $total    = 0;
        $model = M('FileAuth');
        $where = "user_id='{$userID}'";
        $total = $model->where($where)->count();
        $pageObj = new Page($total,$pageSize);
        $authList = $model->where($where)
                          ->limit($pageObj->firstRow. ',' . $pageObj->listRows)
                          ->order('auth_id desc')
                          ->select();
        $page = $this->showPage($pageObj);
        if($total > 0) {
            foreach($authList as $key=>$auth) {
                $fileIDs[] = $auth['rid'];
                $authList[$key]['aType'] = explode(',',$auth['auth_type']);
            }
            $fileModel =  M('Files');
            $fList  = $fileModel->field('id,product_name,project_name,title')
                                ->where('id IN ('.implode(',',$fileIDs).')')
                                ->select();
            foreach($fList as $tmp) {
                $fileMap[$tmp['id']] = $tmp;
            }
            $this->assign('fileMap',$fileMap);
        }
        $this->assign('list',$authList);
        $this->assign("page", $page);
        $this->assign("userType", $userType);
        $this->display('clientFile');
    }
    
    public function image(){
	
	$siteColumnModel = M('SiteColumn');
	$siteColumnList =  $siteColumnModel->select();
	$attachmentModel = M('Attachment');
	
	$img = array();
	
	$defaultColumn = empty($_GET['column_code']) == true ? 'Home Page' : $_GET['column_code'];
	
	//foreach($siteColumnList as $key => $value){
	   //$img[$value['code']] = $attachmentModel->where(" remark = '" . $value['code'] . "' and is_delete = 0")->select();
	//}
	$img = $attachmentModel->where(" remark = '" . $defaultColumn . "' and is_delete = 0")->select();
	//print_r($img);
	$this->assign('siteColumnList',$siteColumnList);
	$this->assign('imgList',$img);
	$this->assign('currentPage',$defaultColumn);
	$this->display();
    }
    
    /**
     * 删除图片 
     */
    public function delImage(){
	$retAry = array('status' => false);
        $con['aid'] = $_POST['id'];
        $data['is_delete'] = 1;
	if(!empty($con['aid']) && $this->_user['Type'] == 1){
	    $retAry = $this->_update('Attachment',$con,$data);
	}
        
        $this->sendJson($retAry);
    }
    
    /**
     * 用于权限设置 
     */
    public function Authority(){
        
        //$fileModel = M('Files');
        //$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
        //$pageSize = 3;
        $userType = $this->_user['Type'];
        
        $account  = '';
        $user_id = $_GET['id'];
        
        if(!empty($user_id) && $userType == 1){
            //只有管理员才可以执行
            $userModel = M('User');
            $tmpReg    = $userModel->where("id = {$user_id}")->select(); 
            $userInfo  = $tmpReg[0];
            $account = $userInfo['account'];
        }

        $editUserType = $userInfo['type'];
        
            //$total = $fileModel->where("is_delete != 0")->count();
            //$pageObj = new Page($total,$pageSize);
            
            /**
             * 待授权数据
             */
        
        $model = new Model();
        
        if($editUserType !=2 ) {
            $data = $model->query(
                "SELECT * FROM 
                    ( SELECT * FROM files WHERE account = '{$account}' AND is_delete = 0 ) AS a 
                    LEFT JOIN 
                    (
                    SELECT * FROM file_auth WHERE is_expired = 0 AND user_id = {$user_id}
                    ) AS b
                    ON a.id = b.rid 
                    ORDER BY a.id DESC ,a.product_id,a.project_id "
                );
        } else {
            $data = $model->query(
                "SELECT * FROM 
                    ( SELECT * FROM files WHERE is_delete = 0 ) AS a 
                    LEFT JOIN 
                    (
                    SELECT * FROM file_auth WHERE is_expired = 0 AND user_id = {$user_id}
                    ) AS b
                    ON a.id = b.rid 
                    ORDER BY a.id DESC ,a.product_id,a.project_id "
                );
        }
        /*
            $data = $fileModel->join("file_auth ON id = file_auth.rid ")
                    ->where("files.account = '{$account}' AND  files.is_delete = 0 ")
                    //->limit($pageObj->firstRow. ',' . $pageObj->listRows)
                    ->order('files.id DESC ,files.product_id,files.project_id')
                    ->select();
        */

        $this->assign('dateCount',count($data));
        $this->assign('data',$data);
        //$this->assign('current_page',$page);
        $this->assign('user_id',$_GET['id']);
        $this->assign('userMsg',$userInfo);
        $this->display();
    }
    
    public function submitAuth(){
        $retAry = array('status' => false);
        
        /*
         * print_r($_POST);
         * Array
            (
            [user_id] => 20
            [auths] => Array
                (
                [0] => 29,view
                [1] => 29,share
                [2] => 28,view
                [3] => 28,share
                )
            )
         * 
         */
        
        if($this->_user['Type'] == 1 && !empty($_POST['auths'])){
            
            $fileAuth = M('FileAuth');
            
            //保存历史模式
            //$con['user_id'] = $_POST['user_id'];
            //$con['is_expired'] = 0;
            //$data['is_expired'] = 1;
            //$data['updatetime']  = date("Y-m-d H:i:s");
            //$data['update_user']  = $this->_user['Account'];
            
            //$retAry = $fileAuth->where($con)->save($data);
            
            //删除历史模式,这里用删除，防止记录膨胀
            $con['where'] = " user_id = " .$_POST['user_id'] ;
            $retAry = $fileAuth->delete($con);
            
            //扁平化
            $authArray = array();
            foreach($_POST['auths'] as $val){
                $one = explode(',',$val);
                
                if(isset($authArray[$one[0]])){
                   $authArray[$one[0]] .= ",".$one[1];
                }else{
                    $authArray[$one[0]] = $one[1];
                }
            }
            
            //然后 insert
            foreach($authArray as $key => $value){
            $d['rid'] = $key;
            $d['user_id'] = $_POST['user_id'];
            $d['auth_type'] = $value;
            
            $d['createtime'] = date("Y-m-d H:i:s");
            $d['updatetime'] = date("Y-m-d H:i:s");
            $d['create_user'] = $this->_user['Account'];
            $d['update_user'] = $this->_user['Account'];
            $d['is_expired'] = 0;
            $fileAuth->add($d);
            }
            
            $retAry = array('status' => true);
        }
        $this->sendJson($retAry);
    }
}