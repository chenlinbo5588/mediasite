<?php

class ProductAction extends CommonAction {
    public function index(){
        die();
    }
    
    /**
     * 获取用户 的产品
     */
    public function getUserProduct(){
	
	$productModel = M('Product');
	
	$html = array();
	
	if(!isset($_GET['user_id'])){
	    $html[] = "<option value=\"\"></option>";
	    $this->sendFormatJson(200,implode('',$html));
	    die();
	}
	if(strpos($_GET['user_id'],',') !== false)
	    $_GET['user_id'] = substr($_GET['user_id'],0,strpos($_GET['user_id'],','));
	
	$where[] = "enable = 1";
	$where[] = "user_id IN ( " .$_GET['user_id'].')';
	$userProduct = $productModel->where(implode(' AND ',$where))->select();
	
	if(!empty($userProduct)){
	    foreach($userProduct as $key => $value){
		$html[] = "<option value=\"{$value['id']},{$value['name']}\">{$value['name']}</option>";
	    }
	}else{
	    $html[] = "<option value=\"\"></option>";
	}
	
	$this->sendFormatJson(200,implode('',$html));
    }
}