<?php

class ProjectAction extends CommonAction {
    public function index(){
        die();
    }
    
    /**
     * 获取 产品对应的项目
     */
    public function getUserProject(){
	$projectModel = M('Project');
	
	$html = array();
	
	if(!isset($_GET['user_id']) || !isset($_GET['product_id'])){
	    $html[] = "<option value=\"\">Empty Project</option>";
	    $this->sendFormatJson(200,implode('',$html));
	    die();
	}
	
	$_GET['user_id'] = substr($_GET['user_id'],0,strpos($_GET['user_id'],','));
	$_GET['product_id'] = substr($_GET['product_id'],0,strpos($_GET['product_id'],','));
	
	$where[] = "enable = 1";
	$where[] = "user_id = " .intval($_GET['user_id']);
	$where[] = "product_id = " .intval($_GET['product_id']);
	
	$userProject = $projectModel->where(implode(' AND ',$where))->select();
	
	if(!empty($userProject)){
	    foreach($userProject as $key => $value){
		$html[] = "<option value=\"{$value['id']},{$value['name']}\">{$value['name']}</option>";
	    }
	}else{
	    $html[] = "<option value=\"\">Empty Project</option>";
	}
	
	$this->sendFormatJson(200,implode('',$html));
    }
}