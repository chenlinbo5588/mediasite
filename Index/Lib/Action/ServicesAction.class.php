<?php
class ServicesAction extends CommonAction {
    public function index(){		
	$attachmentModel = M('Attachment');
	$img = $attachmentModel->where(" remark = 'Services Page' and is_delete = 0")->select();

	$this->assign('imgList',$img);
	$this->assign('imgCount',count($img));
	
        $this->display();
    }

    public function production(){
        $this->display();
    }
}