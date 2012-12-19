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
        
        $data['file_name'] = $attachment['filename'];
        $data['file_size'] = $attachment['filesize'];
        $data['file_suffix'] = $suffix;
        $data['path_name'] = $this->_user['Account'].'/'.$newFilePath;
        $data['createtime'] = date('Y-m-d H:i:s');
        $data['user_account'] = $this->_user['Account'];
        
        $result = $this->_add('Attachment',$data);
        if(isset($result['error'])){
            $this->sendJson(array('respcode' => 0,"source_filename" => $attachment['filename']));
        }else{
            /**
            * 多级目录按照 层级存放 
            */
            $retAry = array('respcode' => 1,'id'=> $result['insertid'] ,'path' => $newFilePath,"source_filename" => $attachment['filename']);
            if(!move_uploaded_file($_FILES['Filedata']['tmp_name'],$userPath.'/'.$newFilePath)){
                $retAry = array('respcode' => 0,"source_filename" => $attachment['filename']);
            }
            
            $this->sendJson($retAry);
        }
    }
}