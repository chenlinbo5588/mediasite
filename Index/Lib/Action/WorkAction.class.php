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
        $infoMsg  = array();
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
            }
        }
        $this->assign('videoMsg',$infoMsg);
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

    public function upload() {
        $editId        = $_GET['id'] ? $_GET['id'] : 0;
        $userType      = $this->_user['Type'];
        if($editId > 0) {
            $fileTypeModel = M('Files');
            $con   = array();
            $con[] = "id={$editId}";
            if($userType != 1) {
                $con[]    = "account = '{$account}'";
                $con[]    = "status = 3";
            }
            $where = implode(' AND ',$con);
            $fileMsg  = $fileTypeModel->where($where)->select();
            $this->assign('fileMsg',$fileMsg[0]);
        }
        $this->assign('sid',Session::detectID());
        $this->display();
    }

    public function share() {
        $editId        = $_GET['id'] ? $_GET['id'] : 0;
        $userType      = $this->_user['Type'];
        if($editId > 0) {
            $fileTypeModel = M('Files');
            $con   = array();
            $con[] = "id={$editId}";
            if($userType != 1) {
                $con[]    = "account = '{$account}'";
                $con[]    = "status = 3";
            }
            $where = implode(' AND ',$con);
            $fileMsg  = $fileTypeModel->where($where)->select();
            $this->assign('fileMsg',$fileMsg[0]);
        }
        $this->display();
    }

    public function saveFiles() {
        $retAry = array('status' => false);
        $editId        = $_POST['id'] ? $_POST['id'] : 0;
        if(!$editId)$this->sendJson($retAry);
        $data['title']       = $_POST['Uploader_Show_Value_2'];
        $data['file_name']   = $_POST['Uploader_Show_Value_2'];
        $data['file_suffix'] = substr($_POST['Uploader_Show_Value_2'],strrpos($_POST['Uploader_Show_Value_2'],'.'));

        if(MAGIC_QUOTES_GPC){
            $_POST['Uploader_Value_1'] = stripslashes($_POST['Uploader_Value_1']);
            $_POST['Uploader_Value_2'] = stripslashes($_POST['Uploader_Value_2']);
        }
        $image = json_decode($_POST['Uploader_Value_1'],true);
        $video = json_decode($_POST['Uploader_Value_2'],true);

        $data['video_path'] = $video['path'];
        $data['video_size'] = $video['size'];
        $data['video_width'] = $_POST['width'];
        $data['video_height'] = $_POST['height'];
        
        $data['img_path'] = $image['path'];
        $data['img_size'] = $image['size'];
        $data['img_width'] = $image['width'];
        $data['img_height'] = $image['height'];
        
        $now = date('Y-m-d H:i:s');
        $data['updatetime'] = $now;
        $data['update_user'] = $this->_user['Account'];
        $retAry = $this->_update('Files',array('id'=>$editId),$data);
        $this->sendJson($retAry);
    }

}