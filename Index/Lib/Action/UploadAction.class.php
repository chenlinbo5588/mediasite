<?php
class UploadAction extends CommonAction {


    /**
     * 上传页面 
     */
    public function index(){
        die(0);
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
        

	$imgTypes = array(
	    "image/jpeg",
	    "image/pjpeg",
	    "image/png",
	    "image/x-png",
	    "image/gif",
	    "image/bmp"
	);

	$isImage = 0;
	if(in_array($_FILES['Filedata']['type'],$imgTypes) || in_array($suffix,array('.jpg','.jpeg','.png','.gif','.bmp'))){
	    $isImage = 1;
	}
	
	$width = 0;
	$height = 0;
	if(1 == $isImage){
	    list($width, $height, $type, $attr) = getimagesize($_FILES['Filedata']['tmp_name']);
	}
		
        $attachmentPath = ROOT_PATH.'/Public/Files/';
        
        $monthDir = date("Ym");
        if(!is_dir($attachmentPath.'/'.$monthDir)){
            mkdir($attachmentPath.'/'.$monthDir);
        }
        
        $newfilename = md5($attachment['filename'].$attachment['filesize'].mt_rand(100, 999));
        $newFilePath = $monthDir.'/'.$newfilename.$suffix;
        
        $data['file_name'] = $attachment['filename'];
        $data['file_size'] = $attachment['filesize'];
        $data['file_suffix'] = $suffix;
        $data['path_name'] = $newFilePath;
        $data['width'] = $width;
        $data['height'] = $height;
        $data['createtime'] = date('Y-m-d H:i:s');
        $data['user_account'] = empty($this->_user['Account']) ? 'remote' : $this->_user['Account'];
        
        $result = $this->_add('Attachment',$data);
        if(isset($result['error'])){
            $this->sendJson(array('respcode' => 0,"source_filename" => $attachment['filename']));
        }else{
            
            $retAry = array('respcode' => 1,'id'=> $result['insertid'] ,'width' => $width,'height'=> $height,'size' => $data['file_size'], 'path' => $newFilePath,"source_filename" => $attachment['filename']);
            if(!move_uploaded_file($_FILES['Filedata']['tmp_name'],$attachmentPath.$newFilePath)){
                $retAry = array('respcode' => 0,"source_filename" => $attachment['filename']);
            }
            
            $this->sendJson($retAry);
        }
    }
}