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
        }
        if($search != '') {
            $scon = array();
            $scon[] = "fid like '%{$search}%'";
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
        $this->display();
    }

}