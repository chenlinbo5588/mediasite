<?php

class FilesModel extends CommonModel {
    protected $trueTableName = 'files';
    
    public $_validate = array(
        array('file_name','file name already exists','',0,'unique',self::MODEL_INSERT)
    );

    public $_auto = array(
        array('createtime','currDateTime',self::MODEL_INSERT,'function'),
        array('updatetime','currDateTime',self::MODEL_UPDATE,'function'),
        array('status',1,self::MODEL_INSERT)
    );
}
