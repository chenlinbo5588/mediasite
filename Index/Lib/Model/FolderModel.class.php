<?php
class FolderModel extends CommonModel {
    public $_validate = array(
        array('name','require','name must'),
        array('project_id','require','project id must'),
        array('user_id','require','user id must')
    );

    public $_auto = array(
        array('enable',1,self::MODEL_INSERT,'string')
    );
}
?>