<?php
class FolderModel extends CommonModel {
    public $_validate = array(
        array('name','folder name already exists','',0,'unique',self::MODEL_INSERT)
        array('project_id','require','project id must'),
        array('user_id','require','user id must')
    );

    public $_auto = array(
        array('createtime','currDateTime',self::MODEL_INSERT,'function'),
        array('updatetime','currDateTime',self::MODEL_UPDATE,'function'),
        array('enable',1,self::MODEL_INSERT,'string')
    );
}
?>