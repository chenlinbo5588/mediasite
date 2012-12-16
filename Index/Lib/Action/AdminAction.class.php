<?php
import("@.ORG.Page");
class AdminAction extends CommonAction {
    public function index(){
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
        $editId = $_GET['uid'] ? $_GET['uid'] : 0;
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

    public function delClient() {
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

    public function product(){
        $search = $_POST['search'];
        $model = M('Product');
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

    public function project(){
        $search = $_POST['search'];
        $model = M('Project');
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

    public function user(){
        $this->display();
    }

}