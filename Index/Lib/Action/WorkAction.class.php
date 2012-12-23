<?php
import("@.ORG.Page");
class WorkAction extends CommonAction {
    public function index(){
        $this->display();
    }

    public function mlist() {
        $userType = $this->_user['Type'];
        $account  = $this->_user['Account'];
        $search   = $_POST['search'];
        $model    = M('Files');
        $page     = 1;
        $pageSize = 10;
        $total    = 0;
        $list     = array();
        $con      = array();
        if($userType != 1) {
            $con[]    = "account = '{$account}'";
            $con[]    = "status = 3";
        }
        if($search != '') {
            $scon = array();
            $scon[] = "id like '%{$search}%'";
            $scon[] = "project_name like '%{$search}%'";
            $scon[] = "product_name like '%{$search}%'";
            $con[]  = '('.implode(' OR ',$scon).')';
        }
        $where = implode(' AND ',$con);
        $total = $model->where($where)->count();
        $pageObj = new Page($total,$pageSize);
        $list   = $model->where($where)
                        ->limit($pageObj->firstRow. ',' . $pageObj->listRows)
                        ->order('createtime desc')
                        ->select();
        $page = $this->showPage($pageObj,$search);
        $this->assign('list',$list);
        $this->assign("page", $page);
        $this->display();
    }

    public function play() {
        $editId   = $_GET['id'] ? $_GET['id'] : 0;
        $category = '';
        $model = M('Files');
        $userType = $this->_user['Type'];
        $account  = $this->_user['Account'];
        if($editId > 0) {
            $con      = array();
            $con[] = "id={$editId}";
            if($userType != 1) {
                $con[]    = "account = '{$account}'";
                $con[]    = "status = 3";
            }
            $where = implode(' AND ',$con);
            $info = $model->where($where)->limit(0,1)->select();
            if(isset($info[0])) {
                $infoMsg  = $info[0];
                $category = $infoMsg['category_name'];
                $this->assign('videoMsg',$infoMsg);
            }
        }
        if($category != '') {
            $page     = 1;
            $pageSize = 4;
            $total    = 0;
            $list     = array();
            $con      = array();
            if($userType != 1) {
                $con[]    = "account = '{$account}'";
                $con[]    = "status = 3";
            }
            $con[] = "category_name='{$category}'";
            $where = implode(' AND ',$con);
            $total = $model->where($where)->count();
            $pageObj = new Page($total,$pageSize);
            $list   = $model->where($where)
                            ->limit($pageObj->firstRow. ',' . $pageObj->listRows)
                            ->order('createtime desc')
                            ->select();
            $page = $this->showPage($pageObj,$search);
            $this->assign('list',$list);
            $this->assign("page", $page);
        }
        $this->display();
    }

}