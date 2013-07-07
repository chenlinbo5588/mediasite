<?php
class FileAction extends CommonAction {
    public function view(){
        $id = $_GET['id'];
        $fileModel = M('Files');
        $files = $fileModel->where("  id = {$id}")->select();
        $video = $files[0];
        if($video['video_width'] == 0)$video['video_width'] = '600';
        if($video['video_height'] == 0)$video['video_width'] = '400';
        $this->assign('videoMsg',$video);

        $mediaExt = explode(',',C('MEDIA_PLAY_EXT'));
        $fileExt = substr(strtolower($video['file_suffix']),1);
        $mediaFlag = in_array($fileExt,$mediaExt);
        $tplName = strtolower($video['category_name']).'_view';
        $this->assign('mediaFlag',$mediaFlag);

        $this->display($tplName);
    }
    
    /**
     * 
     */
    public function submitFile() {
        $retAry = array('status' => false);

        $fileType = strtolower(substr($_POST['file_type'],strpos($_POST['file_type'],',') + 1));
        $titleIndex = 1;
        $image = $video = array();

        switch($fileType){
            case 'movie':
                $titleIndex = 2;

                $image[] = json_decode($_POST['Uploader_Value_1'],true);
                $video[] = json_decode($_POST['Uploader_Value_2'],true);
                break;
            case 'document':
                $titleIndex = 3;
                //use video field to store document file
                foreach($_POST['Uploader_Value_3'] as $tmpFile) {

                    $video[] = json_decode($tmpFile,true);
                }
                break;
            case 'picture':
                $titleIndex = 4;
                foreach($_POST['Uploader_Value_4'] as $tmpFile) {

                    $video[] = json_decode($tmpFile,true);
                    $image[] = json_decode($tmpFile,true);
                }
                break;
            default:
                break;
        }

        $now = date('Y-m-d H:i:s');
        foreach($video as $key=>$tmpData) {
            $data = array();
            if(1 == $this->_user['Type']){
                $data['account'] = substr($_POST['client'],strpos($_POST['client'],',') + 1);
            }else{
                // 普通账户只能给自己加
                $data['account']  = $this->_user['Account'];
            }
            
            $data['product_id'] = substr($_POST['product'],0,strpos($_POST['product'],','));
            $data['product_name'] = substr($_POST['product'],strpos($_POST['product'],',') + 1);
            
            $data['project_id'] = substr($_POST['project'],0,strpos($_POST['project'],','));
            $data['project_name'] = substr($_POST['project'],strpos($_POST['project'],',') + 1);
            
            $data['category_id'] = substr($_POST['file_type'],0,strpos($_POST['file_type'],','));
            $data['category_name'] = substr($_POST['file_type'],strpos($_POST['file_type'],',') + 1);
            $data['title'] = $tmpData['source_filename'];
            $data['file_name'] = $tmpData['source_filename'];
            $data['file_suffix'] = substr($tmpData['source_filename'],strrpos($tmpData['source_filename'],'.'));

            $data['video_path'] = $tmpData['path'];
            $data['video_size'] = $tmpData['size'];
            $data['video_width'] = $_POST['width'];
            $data['video_height'] = $_POST['height'];

            if(!empty($image[$key])){
                $data['img_path'] = $image[$key]['path'];
                $data['img_size'] = $image[$key]['size'];
                $data['img_width'] = $image[$key]['width'];
                $data['img_height'] = $image[$key]['height'];
            }else{
                $data['img_path'] = '';
                $data['img_size'] = 0;
                $data['img_width'] = 0;
                $data['img_height'] = 0;
            }

            if(isset($_POST['id']) && ($_POST['id'] != 0)) {
                $con['id']      = $_POST['id'];
                $data['updatetime'] = $now;
                $data['update_user'] = $this->_user['Account'];
                $retAry = $this->_update('Files',$con,$data);
            } else {
                $data['createtime'] = $now;
                $data['updatetime'] = $now;
                $data['create_user'] = $this->_user['Account'];
                $retAry = $this->_add('Files',$data);
            }
            if(!$retAry['status'])$this->sendJson($retAry);
        }
        $this->sendJson($retAry);
    }

    public function add(){
        $this->edit();
    }
    /**
     * Add or Edit
     */
    public function edit(){
        $fileTypeModel = M('FileType');
        $fileTypeList = $fileTypeModel->where(" enable=1 ")->select();

        $productModel = M('Product');
        $list = array();
        if(1 == $this->_user['Type']){//管理员 
            $userModel = M('User');
            $userList = $userModel->where('enable=1 AND type !=1')
                          ->order('id desc')->select();
            $this->assign('client',$userList);
            $this->assign('showClient',true);
        }else{
            $this->assign('client',array(
                array('id' => $this->_user['ID'] ,'account' => $this->_user['Account'])
            ));
             $this->assign('showClient',false);
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
    
    
    /**
     * 审核 
     */
    public function verify(){
        
        if(1 == $this->_user['Type']){
            $fileModel = M('Files');
            $id = $_GET['id'];
            $data = $fileModel->where(" id = {$id}")->select();

            foreach($data as $key => $value){
                $this->assign('data',$value);
            }
            $this->display();
        }else{
            echo "<div>No Access</div>";
        }
    }
    
    public function verifySubmit(){
        
        $retAry = array('status' => false);

        if(1 == $this->_user['Type']){
            $now = date('Y-m-d H:i:s');
            $con['id']      = $_POST['id'];
            $data['status'] = $_POST['status'];
            $data['remark'] = $_POST['remark'];
            $data['updatetime'] = $now;
            $data['update_user'] = $this->_user['Account'];
 
            $retAry = $this->_update('Files',$con,$data);
        }
        
        $this->sendJson($retAry);
    }

    public function deleteFile(){
        $retAry = array('status' => false);

        if(1 == $this->_user['Type']){
            $fileModel = M('Files');
            $ret = $fileModel->where(" id = ".(empty($_POST['id']) == true ? 0 : $_POST['id']))->select();
            if(empty($ret[0])){
                $this->sendJson($retAry);
            }
            $info = $ret[0];
            $deleteFile = ROOT_PATH.'/Public/Files/'.$info['video_path'];
            if(file_exists($deleteFile)){
                $isDelete = @unlink($deleteFile);
            }else{
                $isDelete = true;
            }
            if(!$isDelete) $this->sendJson($retAry);
            $deleteFile = ROOT_PATH.'/Public/Files/'.$info['img_path'];
            if(file_exists($deleteFile)){
                @unlink($deleteFile);
            }
            $now = date('Y-m-d H:i:s');
            $con['id']      = $_POST['id'];
            $data['is_delete'] = 1;
            $data['file_name']  = time();
            $data['updatetime'] = $now;
            $data['update_user'] = $this->_user['Account'];
            $retAry = $this->_update('Files',$con,$data);
            if($retAry['status']){
                $fileAuth = M('FileAuth');
                $con['where'] = "rid = " .$_POST['id'];
                $fileAuth->delete($con);
            }
        }
        $this->sendJson($retAry);
    }
    
    /**
     * 多文件删除时，用于删除队列中的文件 
     */
    public function deleteQueuedFile(){
	
	$retAry = array('status' => false);
	
	$attachment = M('Attachment');
	$ret = $attachment->where(" aid = ".(empty($_POST['id']) == true ? 0 : $_POST['id']))->select();
	if(empty($ret[0])){
	    //找不到记录也试为删除成功
	    $this->sendJson(array('status' => true));
	}
	$info = $ret[0];
	$deleteFile = ROOT_PATH.'/Public/Files/'.$info['path_name'];
	if(file_exists($deleteFile)){
	    $isDelete = @unlink($deleteFile);
	}else{
	    $isDelete = true;
	}
	if(!$isDelete) $this->sendJson($retAry);
	
	$con['aid']      = $_POST['id'];
	$data['is_delete'] = 1;

	$retAry = $this->_update('Attachment',$con,$data);
	$this->sendJson($retAry);
    }
    
    public function checkAuth(){
	$retAry = array('status' => false,'message' => 'No Authority');
	
	$fileId = empty($_POST['id']) ? 0 : $_POST['id'];
	$authType = $_POST['auth_type'];
	
	if(empty($authType)){
	    $this->sendJson($retAry);
	}
	
	$user_id = empty($this->_user['ID']) ? 0 : $this->_user['ID'];
	$fileAuthModel = M('FileAuth');
	$dataRow = $fileAuthModel->where("rid = {$fileId} AND user_id = {$user_id}")->select();

	if(!empty($dataRow)){
	    $auths = explode(',',$dataRow[0]['auth_type']);	    
	    foreach($auths as $v){
		if($authType == $v){
		    $retAry['status'] = true;
		    $retAry['message'] = 'Check Authority Success';
		    break;
		}
	    }
	}
	
	$this->sendJson($retAry);
	
    }
}
