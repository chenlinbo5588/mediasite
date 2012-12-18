<?php
class UploadAction extends CommonAction {


    /**
     * 上传页面 
     */
    public function index(){
        $this->assign('sid',Session::detectID());
        $this->display();
    }
    
    
    /**
     * 上传处理 
     */
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
         * @todo db insert ,待完成 需要建表
         */
        $insertId = 1;
        /**
         * 服务器path 
         */
        
        $suffix = substr($attachment['filename'],strrpos($attachment['filename'],'.'));
        $suffix = strtolower($suffix);
        
        $attachmentPath = ROOT_PATH.'/Public/attachment/';
        $userPath = $attachmentPath.$this->_user['Account'];
        if(!is_dir($userPath)){
            mkdir($userPath);
        }
        
        $monthDir = date("Ym");
        if(!is_dir($userPath.'/'.$monthDir)){
            mkdir($userPath.'/'.$monthDir);
        }
        
        $newfilename = md5($attachment['filename'].$attachment['filesize'].mt_rand(100, 999));
        $newFilePath = $monthDir.'/'.$newfilename.$suffix;
        
        /**
         * 多级目录按照 层级存放 
         */
        $retAry = array('respcode' => 1,'id'=> $insertId ,'path' => $newFilePath,"source_filename" => $attachment['filename']);
        if(!move_uploaded_file($_FILES['Filedata']['tmp_name'],$userPath.'/'.$newFilePath)){
            $retAry = array('respcode' => 0,"source_filename" => $attachment['filename']);
        }
        
        $this->sendJson($retAry);
    }
}