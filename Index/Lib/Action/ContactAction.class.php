<?php
class ContactAction extends CommonAction {
    public function index(){
		
	$attachmentModel = M('Attachment');
	$img = $attachmentModel->where(" remark = 'Contact Page' and is_delete = 0")->select();

	$this->assign('imgList',$img);
	$this->assign('imgCount',count($img));
	
        $this->display();
    }

}