<?php
class ShareFilesModel extends CommonModel {

    public $_auto = array(
        array('createtime','currDateTime',self::MODEL_INSERT,'function'),
        array('enable',1,self::MODEL_INSERT)
    );
}
?>