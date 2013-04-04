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
        $this->assign('mediaFlag',$mediaFlag);

        $this->display();
    }
    
    /**
     * 
     */
    public function submitFile() {
        $retAry = array('status' => false);

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
        
	/**
	 * 如果 magic_quotes_gpc = On ,则需要处理 
	 */
	if(MAGIC_QUOTES_GPC){
	    $_POST['Uploader_Value_1'] = stripslashes($_POST['Uploader_Value_1']);
	    $_POST['Uploader_Value_2'] = stripslashes($_POST['Uploader_Value_2']);
            $_POST['Uploader_Value_3'] = stripslashes($_POST['Uploader_Value_3']);
	}
        
        $fileType = strtolower($data['category_name']);
        $titleIndex = 1;
        switch($fileType){
            case 'movie':
                $titleIndex = 2;
                $image = json_decode($_POST['Uploader_Value_1'],true);
                $video = json_decode($_POST['Uploader_Value_2'],true);
                break;
            case 'document':
                $titleIndex = 3;
                //use video field to store document file
                $video = json_decode($_POST['Uploader_Value_3'],true);
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
        
        $data['title'] = $_POST['Uploader_Show_Value_'.$titleIndex];
	$data['file_name'] = $_POST['Uploader_Show_Value_'.$titleIndex];
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
            $userList = $userModel->where('enable=1 AND type=0')
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
            $ret = $fileModel->where(" id = ".$_POST['id'])->select();
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
        }
        $this->sendJson($retAry);
    }
}
