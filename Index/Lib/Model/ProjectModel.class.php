<?php
class ProjectModel extends CommonModel {
    public $_validate = array(
        array('name','require','name must')
    );

    public $_auto = array(
        array('createtime','currDateTime',self::MODEL_INSERT,'function'),
        array('updatetime','currDateTime',self::MODEL_UPDATE,'function'),
        array('enable',1,self::MODEL_INSERT,'string')
    );
}
?>