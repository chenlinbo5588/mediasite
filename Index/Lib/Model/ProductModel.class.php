<?php
class ProductModel extends CommonModel {
    public $_validate = array(
        array('name','require','name already exists',0,'unique')
    );

    public $_auto = array(
        array('createtime','currDateTime',self::MODEL_INSERT,'function'),
        array('updatetime','currDateTime',self::MODEL_UPDATE,'function'),
        array('enable',1,self::MODEL_INSERT,'string')
    );
}
?>