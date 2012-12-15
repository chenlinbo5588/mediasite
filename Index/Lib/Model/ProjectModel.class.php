<?php
class ProjectModel extends CommonModel {
    public $_validate = array(
        array('name','require','name must')
    );

    public $_auto = array(
        array('enable',1,self::MODEL_INSERT,'string')
    );
}
?>