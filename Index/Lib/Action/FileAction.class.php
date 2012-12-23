<?php
class FileAction extends CommonAction {
    public function view(){
        $id = $_GET['id'];
        $fileModel = M('Files');
        $files = $fileModel->where("  id = {$id}")->select();
        
        foreach($files as $key => $data){
            $this->assign('video',$data);
        }
        $this->display();
    }
    
    /**
     * 审核 
     */
    public function verify(){
        
        if(1 == $this->_user['Type']){
            $this->display();
        }else{
            echo "<div>No Access</div>";
        }
    }
}
