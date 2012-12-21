<?php
import("@.ORG.Page");
class AdminAction extends CommonAction {
    public function index(){
        if($this->_user['Type'] == 1) {
            redirect(__URL__ . '/client');
        } else {
            redirect(__URL__ . '/upload');
        }
    }

    public function client(){
        $search = $_POST['search'];
        $model = M('User');
        $page     = 1;
        $pageSize = 10;
        $total    = 0;
        $list = array();
        $con = array('enable=1','type=0');
        if($search != '') {
            $scon = array();
            $scon[] = "id like '%{$search}%'";
            $scon[] = "account like '%{$search}%'";
            $scon[] = "nickname like '%{$search}%'";
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
        $this->display();
    }

    public function editClient() {
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

    public function submitClient() {
        $retAry = array('status' => false);
        if(isset($_POST['id']) && ($_POST['id'] != 0)) {
            $con['id']      = $_POST['id'];
            $data = $_POST;
            unset($data['id']);
            $retAry = $this->_update('User',$con,$data);
        } else {
            $data  = $_POST;
            $data['enable'] = 1;
            $data['createtime'] = date('Y-m-d H:i:s');
            $retAry = $this->_add('User',$data);
        }
        $this->sendJson($retAry);
    }

    public function delClient() {
        $retAry = array('status' => false);
        $editId = $_POST['id'];
        $con['id']      = $editId;
        $data['enable'] = 2;
        $retAry = $this->_update('User',$con,$data);
        if($retAry['status']) {
            $pcon['user_id'] = $editId;
            $this->_update('Pruduct',$pcon,$data);
            $this->_update('Project',$pcon,$data);
            $this->_update('Folder',$pcon,$data);
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
        if($_GET['uid'] > 0)$con[] = 'user_id IN ('.$_GET['uid'].')';
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
            $client = $model->field('product.name AS Name,user.nickname AS Nickname,user.account AS Account')
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

    public function project(){
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
        if($_GET['pid'] > 0)$con[] = 'product_id IN ('.$_GET['pid'].')';
        $where = implode(' AND ',$con);
        $total = $model->where($where)->count();
        $pageObj = new Page($total,$pageSize);
        $list   = $model->field('project.id AS ID,project.name AS Name,product.name AS ProductName,user.nickname AS Nickname,user.account AS Account')
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

    public function editProject() {
        $editId  = $_GET['id'] ? $_GET['id'] : 0;
        $editMsg = array();
        if($editId) {
            $model  = M('Project');
            $client = $model->field('project.name AS Name,product.name AS ProductName,user.nickname AS Nickname,user.account AS Account')
                            ->join('product ON product.id = project.product_id')
                            ->join('user ON user.id = project.user_id')
                            ->where('project.id={$editId}')
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

    public function submitProject() {
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

    public function delProject() {
        $retAry = array('status' => false);
        $userId = $_POST['uid'];
        $con['id']      = $userId;
        $data['enable'] = 2;
        $retAry = $this->_update('User',$con,$data);
        if($retAry['status']) {
            $pcon['user_id'] = $userId;
            $this->_update('Prudcut',$pcon,$data);
            $this->_update('Project',$pcon,$data);
            $this->_update('Folder',$pcon,$data);
        }
        $this->sendJson($retAry);
    }
    
    public function file(){
        
        $this->display();
    }
    
    
    public function submitFile() {
        $retAry = array('status' => false);

	/*
	Uploader_Show_Value_1	IMG_29112012_104101.png
	Uploader_Show_Value_2	meng_qian.flv
	Uploader_Value_1	{"respcode":1,"id":18,"width":664,"height":276,"path":"201212\/3888d7b2b545b1866aa3a6f9d098106b.png","source_filename":"IMG_29112012_104101.png"}
	Uploader_Value_2	{"respcode":1,"id":19,"width":0,"height":0,"path":"201212\/f68e9be04317f278b192fd2e52d9815c.flv","source_filename":"meng_qian.flv"}
	client	1,admin
	fid	编辑时才有
	file_type	1,movie
	height	450
	product	1,Product1
	projct	1,Project1
	width	600
	*/
	
	$data = array();
	
	$data['account'] = addslashes(substr($_POST['client'],strpos($_GET['client'],',') + 1));
	$data['title'] = addslashes($_POST['Uploader_Show_Value_2']);
	$data['file_name'] = addslashes($_POST['Uploader_Show_Value_2']);
	$data['file_suffix'] = substr($_POST['Uploader_Show_Value_2'],strrpos($_POST['Uploader_Show_Value_2'],'.'));
	
	/**
	 * 如果 magic_quotes_gpc = On ,则需要处理 
	 */
	if(MAGIC_QUOTES_GPC){
	    //$_POST['Uploader_Value_1'] = str_replace(array("\\\\", "\\\""), array("\\", "\""), $_POST['Uploader_Value_1']); 
	    //$_POST['Uploader_Value_2'] = str_replace(array("\\\\", "\\\""), array("\\", "\""), $_POST['Uploader_Value_2']);
	    $_POST['Uploader_Value_1'] = stripslashes($_POST['Uploader_Value_1']);
	    $_POST['Uploader_Value_2'] = stripslashes($_POST['Uploader_Value_2']);
	    
	}
	$image = json_decode($_POST['Uploader_Value_1'],true);
	$video = json_decode($_POST['Uploader_Value_2'],true);
	
	$data['video_path'] = $video['path'];
	$data['video_size'] = $video['size'];
	$data['video_width'] = $video['width'];
	$data['video_height'] = $video['height'];
	
	$data['img_path'] = $image['path'];
	$data['img_size'] = $image['size'];
	$data['img_width'] = $image['width'];
	$data['img_height'] = $image['height'];
	
	
	$data['product_id'] = addslashes(substr($_POST['product'],0,strpos($_POST['product'],',')));
	$data['product_name'] = addslashes(substr($_POST['product'],strpos($_POST['product'],',') + 1));
	
	$data['project_id'] = addslashes(substr($_POST['project'],0,strpos($_POST['project'],',')));
	$data['project_name'] = addslashes(substr($_POST['project'],strpos($_POST['project'],',') + 1));
	
	$data['category_id'] = addslashes(substr($_POST['file_type'],0,strpos($_POST['file_type'],',')));
	$data['category_name'] = addslashes(substr($_POST['file_type'],strpos($_POST['file_type'],',') + 1));
	
	$now = date('Y-m-d H:i:s');
	
        if(isset($_POST['fid']) && ($_POST['fid'] != 0)) {
            $con['fid']      = $_POST['fid'];
	    $data['updatetime'] = $now;
	    $data['update_user'] = $this->_user['Account'];
            $retAry = $this->_update('Files',$con,$data);
        } else {
            $data['createtime'] = $now;
            $data['updatetime'] = $now;
	    $data['create_user'] = $this->_user['Account'];
            $retAry = $this->_add('Files',$data);
        }
        $this->sendJson($retAry);
    }
    
    public function editFile(){
	
	$fileTypeModel = M('FileType');
	$fileTypeList = $fileTypeModel->where(" enable=1 ")->select();
	
	$productModel = M('Product');
        $list = array();
        
        if('admin' == $this->_user['Account']){
            $userModel = M('User');
            $userList = $userModel->where(" enable=1 ")->select();
            $this->assign('client',$userList);
        }
        
        $con = array();
        $con[] = "user_id = " . $this->_user['ID'];
        $con[] = 'enable=1';
        
        $where = implode(' AND ',$con);
        $list   = $productModel->where($where)
                        ->order('id desc')
                        ->select();
	
	$this->assign('fileType',$fileTypeList);
        $this->assign('product',$list);
        $this->assign('sid',Session::detectID());
        $this->display();
    }
    
    public function folder(){
        $search = $_POST['search'];
        $model = M('Folder');
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

    public function editFolder() {
        $editId = $_GET['id'] ? $_GET['id'] : 0;
        if($editId) {
            $model = M('Product');
            $client = $model->where("id={$editId}")->limit(0,1)->select();
            if(isset($client[0])) {
                $clientMsg = $client[0];
                $this->assign('vo',$clientMsg);
            }
        }
        $this->display();
    }

    public function submitFolder() {
        $retAry = array('status' => false);
        if(isset($_POST['id']) && ($_POST['id'] != '')) {
            $con['id']      = $_POST['id'];
            $data = $_POST;
            unset($data['id']);
            $retAry = $this->_update('User',$con,$data);
        } else {
            $data  = $_POST;
            $data['enable'] = 1;
            $data['createtime'] = date('Y-m-d H:i:s');
            $retAry = $this->_add('User',$data);
        }
        $this->sendJson($retAry);
    }

    public function delFolder() {
        $retAry = array('status' => false);
        $userId = $_POST['uid'];
        $con['id']      = $userId;
        $data['enable'] = 2;
        $retAry = $this->_update('User',$con,$data);
        if($retAry['status']) {
            $pcon['user_id'] = $userId;
            $this->_update('Prudcut',$pcon,$data);
            $this->_update('Project',$pcon,$data);
            $this->_update('Folder',$pcon,$data);
        }
        $this->sendJson($retAry);
    }

    public function user(){
        $this->display();
    }

}