<?php

/**
 * 网站栏目页面 图片维护 
 */
class ImageAction extends CommonAction {
    
    /**
     * 提交
     */
    public function submitImage() {
        $retAry = array('status' => false);

	$data = array();
        if(1 == $this->_user['Type']){
            $data['account'] = substr($_POST['client'],strpos($_POST['client'],',') + 1);
        }else{
            // 普通账户只能给自己加
            $data['account']  = $this->_user['Account'];
        }
	$data['title'] = $_POST['Uploader_Show_Value_2'];
	$data['file_name'] = $_POST['Uploader_Show_Value_2'];
	$data['file_suffix'] = substr($_POST['Uploader_Show_Value_2'],strrpos($_POST['Uploader_Show_Value_2'],'.'));
	
	/**
	 * 如果 magic_quotes_gpc = On ,则需要处理 
	 */
	if(MAGIC_QUOTES_GPC){
	    $_POST['Uploader_Value_1'] = stripslashes($_POST['Uploader_Value_1']);
	    $_POST['Uploader_Value_2'] = stripslashes($_POST['Uploader_Value_2']);
	}
	$image = json_decode($_POST['Uploader_Value_1'],true);
	
	$data['img_path'] = $image['path'];
	$data['img_size'] = $image['size'];
	$data['img_width'] = $image['width'];
	$data['img_height'] = $image['height'];
	
	/*
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
	 * 
	 */
    }
    
    public function add(){
        $this->edit();
    }
    /**
     * Add or Edit
     */
    public function edit(){
        $siteColumnModel = M('SiteColumn');
        $list = $siteColumnModel->where(" 1=1 ")->select();

	$this->assign('siteColumn',$list);
	$this->assign('imageCount',array(1,2,3,4,5));
        $this->assign('sid',Session::detectID());
        $this->display();
    }
    
}
