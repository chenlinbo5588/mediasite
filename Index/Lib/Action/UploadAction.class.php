<?php
class UploadAction extends CommonAction {


	
    public function index(){


        $this->display();
    }
    
    
    public function upload(){
        /**
         * 上传处理
         */
        
        $attachment = array(
            'filename'  => $_FILES['Filedata']['name'],
            'filesize'  => $_FILES['Filedata']['size'],
            'type'		=> $_FILES["Filedata"]["type"]
        );
        
        /**
         *@todo db insert  
         */
        
        /**
         * 服务器path 
         */
        
        echo json_encode(array('respcode' => 0,'id'=>1,'path' => 'file/1/1.mp3',"filename" => $attachment['filename']));

        
    }

}