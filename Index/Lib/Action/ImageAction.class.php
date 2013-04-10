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
	
	$imageId = array() ;
	for($i = 1; $i <= 5 ; $i++){
	    if(!empty($_POST['Uploader_Value_'.$i])){
		
		$tmp = json_decode($_POST['Uploader_Value_'.$i],true);
		$imageId[] = $tmp['id'];
	    }
	}
	
        if(!empty($imageId) && !empty($_POST['column_code'])) {
	    if(count($imageId) > 1){
		$condition = "aid IN (" . implode(',',$imageId) . ")";
	    }else{
		$condition = "aid = " .$imageId[0];
	    }
	    $data['remark'] = $_POST['column_code'];
            $retAry = $this->_update('Attachment',$condition,$data);
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
        $siteColumnModel = M('SiteColumn');
        $list = $siteColumnModel->where(" 1=1 ")->select();

	$this->assign('siteColumn',$list);
	$this->assign('imageCount',array(1,2,3,4,5));
        $this->assign('sid',Session::detectID());
        $this->display();
    }
    
}
