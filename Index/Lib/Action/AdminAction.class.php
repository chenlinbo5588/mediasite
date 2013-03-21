<?php
class AdminAction extends CommonAction {
    public function index(){
        if($this->_user['Type'] == 1) {
            redirect(__URL__ . '/user');
        } else {
            redirect(__URL__ . '/file');
        }
    }

    public function user(){
        $search = $_POST['search'];
        $model = M('User');
        $page     = 1;
        $pageSize = 10;
        $total    = 0;
        $list = array();
        $con = array('enable=1');
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
            $retAry = $this->_update('User',$con,$data);
            if(($data['enable'] == '0') && $retAry['status']) {
                $pcon['enable'] = $data['enable'];
                $pcon['user_id'] = $data['id'];
                $this->_update('Pruduct',$pcon,$data);
                $this->_update('Project',$pcon,$data);
            }
        } else {
            $data  = $_POST;
            $data['enable'] = 1;
            $data['createtime'] = date('Y-m-d H:i:s');
            $retAry = $this->_add('User',$data);
        }
        $this->sendJson($retAry);
    }

    public function delUser() {
        $retAry = array('status' => false);
        $editId = $_POST['id'];
        $con['id']      = $editId;
        $data['enable'] = 2;
        $retAry = $this->_update('User',$con,$data);
        if($retAry['status']) {
            $pcon['user_id'] = $editId;
            $this->_update('Pruduct',$pcon,$data);
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
                          ->where('enable=1 AND type=0')
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
        $userMod = M('Product');
        $list   = $userMod->field('id,name')
                          ->where('enable=1')
                          ->order('id desc')
                          ->select();
        $this->assign('productList',$list);
        $userMod = M('User');
        $list   = $userMod->field('id,nickname,account')
                          ->where('enable=1 AND type=0')
                          ->order('id desc')
                          ->select();
        $this->assign('userList',$list);
        $this->display();
    }

    public function submitFolder() {
        $retAry = array('status' => false);
        if(isset($_POST['id']) && ($_POST['id'] != '')) {
            $con['id']      = $_POST['id'];
            $data = $_POST;
            unset($data['id']);
            $retAry = $this->_update('Project',$con,$data);
        } else {
            $data  = $_POST;
            $data['enable'] = 1;
            $retAry = $this->_add('Project',$data);
        }
        $this->sendJson($retAry);
    }

    public function delFolder() {
        $retAry = array('status' => false);
        $editId = $_POST['id'];
        $con['id']      = $editId;
        $data['enable'] = 2;
        $retAry = $this->_update('Project',$con,$data);
        $this->sendJson($retAry);
    }
    
    public function file(){
        $search = $_POST['search'];
        $model = M('Files');
        $page     = 1;
        $pageSize = 10;
        $total    = 0;
        $userType = $this->_user['Type'];
        $account  = $this->_user['Account'];
        $list = array();
        $con = array();
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
        $this->assign("userType", $userType);
        if($userType == 1) {
            $tplName = 'file';
        }
        else {
            $tplName = 'clientFile';
        }
        $this->display($tplName);
        
    }
    
    public function share() {
        $editId   = $_GET['id'] ? $_GET['id'] : 0;
        $tplName   = $_GET['down'] == 1 ? 'download' : 'share';
        $userType = $this->_user['Type'];
        $account  = $this->_user['Account'];
        if($editId > 0) {
            $fileTypeModel = M('Files');
            $con   = array();
            $con[] = "id={$editId}";
            if($userType != 1) {
                $con[]    = "account = '{$account}'";
            }
            $where = implode(' AND ',$con);
            $fileMsg  = $fileMsg[0];
            $this->assign('fileMsg',$fileMsg);

            if($_GET['down'] != 1) {
                $encodeStr = $this->encodeInfo($editId);
                $fileUrl = 'http://'.ROOT_APP_URL."/Work/play/share/{$encodeStr}";
                $this->assign('fileUrl',$fileUrl);
            } else {
                $downloadName = $fileMsg['product_name'].'_'.$fileMsg['project_name'].$fileMsg['file_suffix'];
                $this->assign('downloadName',$downloadName);
            }
        }
        $this->display($tplName);
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
}