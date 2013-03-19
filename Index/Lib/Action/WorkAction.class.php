<?php
import("@.ORG.Util.Page");
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
            //$con[]    = "status = 3";
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
        $share    = $_GET['share'] ? $_GET['share'] : '';
        $category = '';
        $infoMsg  = array();
        $model = M('Files');
        $userType = $this->_user['Type'];
        $account  = $this->_user['Account'];
        $con      = '';
        if($editId > 0) {
            $con[] = "id={$editId}";
            if($userType != 1) {
                $con[]    = "account = '{$account}'";
                $con[]    = "status = 3";
            }
        } else if($share) {
            $shareId = $this->decodeInfo($share);
            $con[] = "id={$shareId}";
        }
        if($con != '') {
            $where = implode(' AND ',$con);
            $info = $model->where($where)->limit(0,1)->select();
            if(isset($info[0])) {
                $infoMsg  = $info[0];
                $category = $infoMsg['category_name'];
            }
        }
        $this->assign('videoMsg',$infoMsg);
        if($category != '' && $editId) {
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
        $editId   = $_GET['id'] ? $_GET['id'] : 0;
        $userType = $this->_user['Type'];
        $account  = $this->_user['Account'];
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
                $con[]    = "status = 3";
            }
            $where = implode(' AND ',$con);
            $fileMsg  = $fileTypeModel->where($where)->select();
            $this->assign('fileMsg',$fileMsg[0]);

            if($_GET['down'] != 1) {
                $encodeStr = $this->encodeInfo($editId);
                $fileUrl = 'http://'.UPLOAD_DOMAIN."/Work/play/share/{$encodeStr}";
                $this->assign('fileUrl',$fileUrl);
            }
        }
        $this->display($tplName);
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

    public function submitShare() {
        $retAry = array('status' => false);
        $shareID    = $_POST['id'];
        if($shareID <= 0) {$this->sendJson($retAry);}
        $encodeStr = $this->encodeInfo($shareID);
        $fileUrl = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__."/Work/play/share/{$encodeStr}";
        $targetMail = $_POST['target_email'];
        $mail       = $_POST['email'];
        $message    = $_POST['message'];
        if($targetMail == '' || $mail == '') {
            $retAry['error'] = 'Please input emails.';
            $this->sendJson($retAry);
        }
        $mailSubject = "Share Files From {$mail}";
        $mailTpl     = $this->loadMailTpl('shareMail');
        $mailBody    = str_replace(array('{TARGETMAIL}','{MESSAGE}','{SHAREURL}'),array($mail,$message,$fileUrl),$mailTpl);
        $retAry     = sendmail($mailSubject,$mailBody,array(array($targetMail,$targetMail)),array($mail,$mail));
        if($retAry['status']) {
            $shareData = array('url'          => $fileUrl,
                               'email'        => $mail,
                               'target_email' => $targetMail,
                               'message'      => $message,
                               'user_id'      => $this->_user['ID']);
            $retAry = $this->_add('ShareFiles',$shareData);
        }
        $this->sendJson($retAry);
    }

    public function download() {
        $editId   = $_POST['id'] ? $_POST['id'] : 0;
        $userType = $this->_user['Type'];
        $account  = $this->_user['Account'];
        if($editId <= 0)die;
        $fileTypeModel = M('Files');
        $con   = array();
        $con[] = "id={$editId}";
        if($userType != 1) {
            $con[]    = "account = '{$account}'";
            $con[]    = "status = 3";
        }
        $where = implode(' AND ',$con);
        $fileMsg  = $fileTypeModel->where($where)->select();
        $fileMsg  = $fileMsg[0];
        $downloadName = $fileMsg['product_name'].'_'.$fileMsg['project_name'].$fileMsg['file_suffix'];

        $fileName = $fileMsg['video_path']; 
        $fileDir  = ROOT_PATH . '/Public/Files/'; 
        $filePath = $fileDir . $fileName;
        if (!file_exists($filePath)) {
            die("File is not exist:".$filePath); 
        } else {
            $file = fopen($filePath,"r");
            Header("Content-type: application/octet-stream"); 
            Header("Accept-Ranges: bytes"); 
            Header("Accept-Length: ".filesize($filePath)); 
            Header("Content-Disposition: attachment; filename=" . $downloadName);
            echo fread($file,filesize($filePath)); 
            fclose($file); 
            die;
        }
    }

}