<?php
import("@.ORG.Util.Page");
class WorkAction extends CommonAction {

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
        $con[] = 'is_delete<>1';
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
        $this->assign("userType", $userType);
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
                //$con[]    = "status = 3";
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

        if($editId > 0 && (strtolower($category) == 'picture')) {
            $imgList[] = $infoMsg;
            $con      = array();
            if($userType != 1) {
                $con[]    = "account = '{$account}'";
                //$con[]    = "status = 3";
            }
            $con[] = "category_name='{$category}'";
            $con[] = "project_id=".$infoMsg['project_id'];
            $con[] = "id!='{$editId}'";
            $where = implode(' AND ',$con);
            $total = $model->where($where)->count();
            if($total > 0) {
                $imgList   = $model->where($where)
                                ->limit('0,4')
                                ->order('createtime desc')
                                ->select();
                array_unshift($imgList,$infoMsg);
            }
            $this->assign('imgList',$imgList);
        }

        $mediaExt = explode(',',C('MEDIA_PLAY_EXT'));
        $fileExt = substr(strtolower($infoMsg['file_suffix']),1);
        $mediaFlag = in_array($fileExt,$mediaExt);
        $this->assign('mediaFlag',$mediaFlag);

        if($category != '' && $editId) {
            $page     = 1;
            $pageSize = 4;
            $total    = 0;
            $list     = array();
            $con      = array();
            $where    = '';
            if($userType != 1) {
                $con[]    = "account = '{$account}'";
                //$con[]    = "status = 3";
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
            $this->assign("currPage", $_GET['page'] ? $_GET['page'] : 1);
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
                //$con[]    = "status = 3";
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
                //$con[]    = "status = 3";
            }
            $where = implode(' AND ',$con);
            $fileMsg  = $fileTypeModel->where($where)->select();
            $fileMsg  = $fileMsg[0];
            if($userType == 1) {
                $account = $fileMsg['account'];
            }
            $this->assign('fileMsg',$fileMsg);

            if($_GET['down'] != 1) {
                $encodeStr = $this->encodeInfo($editId);
                $fileUrl = 'http://'.ROOT_APP_URL."/Work/play/share/{$encodeStr}";
                $this->assign('fileUrl',$fileUrl);
            } else {
                $downloadName = $fileMsg['product_name'].'_'.$fileMsg['project_name'].$fileMsg['file_suffix'];
                $this->assign('downloadName',$downloadName);
                $this->assign('userAccount',$account);
            }
        }
        $this->display($tplName);
    }

    public function saveFiles() {
        $retAry = array('status' => false);
        $editId        = $_POST['id'] ? $_POST['id'] : 0;
        if(!$editId)$this->sendJson($retAry);
        
        if(MAGIC_QUOTES_GPC){
            $_POST['Uploader_Value_1'] = stripslashes($_POST['Uploader_Value_1']);
            $_POST['Uploader_Value_2'] = stripslashes($_POST['Uploader_Value_2']);
        }
        
        $fileType = $_POST['file_type'];
        $titleIndex = 1;
        
        switch($fileType){
            case 'movie':
                $titleIndex = 2;
                $image = json_decode($_POST['Uploader_Value_1'],true);
                $video = json_decode($_POST['Uploader_Value_2'],true);
                break;
            case 'document':
                $titleIndex = 1;
                //use video field to store document file
                $video = json_decode($_POST['Uploader_Value_1'],true);
                $image = array();
                break;
            case 'picture':
                $titleIndex = 1;
                $image = json_decode($_POST['Uploader_Value_1'],true);
                $video = $image;
                break;
            default:
                break;
        }
        
        $data['title']       = $_POST['Uploader_Show_Value_'.$titleIndex];
        $data['file_name']   = $_POST['Uploader_Show_Value_'.$titleIndex];
        $data['file_suffix'] = substr($_POST['Uploader_Show_Value_'.$titleIndex],strrpos($_POST['Uploader_Show_Value_'.$titleIndex],'.'));
        
        $data['video_path'] = $video['path'];
        $data['video_size'] = $video['size'];
        $data['video_width'] = $_POST['width'];
        $data['video_height'] = $_POST['height'];
        
        if(!empty($image)){
            $data['img_path'] = $image['path'];
            $data['img_size'] = $image['size'];
            $data['img_width'] = $image['width'];
            $data['img_height'] = $image['height'];
        }else{
            $data['img_path'] = '';
            $data['img_size'] = 0;
            $data['img_width'] = 0;
            $data['img_height'] = 0;
        }
        
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
        $fileUrl = 'http://'.ROOT_APP_URL."/Work/play/share/{$encodeStr}";
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
        $account  = trim($_POST['user-account']);
        if($editId <= 0 || ($account == ''))exit(0);
        $fileTypeModel = M('Files');
        $con   = array();
        $con[] = "id={$editId}";
        if($userType != 1) {
            $con[]    = "account = '{$account}'";
            //$con[]    = "status = 3";
        }
        $where = implode(' AND ',$con);
        $fileMsg  = $fileTypeModel->where($where)->select();
        $fileMsg  = $fileMsg[0];
        $downloadName = $fileMsg['product_name'].'_'.$fileMsg['project_name'].$fileMsg['file_suffix'];

        /*
            $fileDir  = 'http://'.UPLOAD_DOMAIN . '/Public/Files/'; 
            $filePath = $fileDir . $fileName;
            import('@.ORG.Net.Http');
            $downHttp = new Http();
            $downHttp->fsockopenDownload($filePath);
        */
        downloadFile($downloadName,ROOT_PATH.'/Public/Files/'.$fileMsg['video_path']);
    }
    
}