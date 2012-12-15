<?php
class ProductModel extends CommonModel {
    public $_validate = array(
        array('name','name already exists','',0,'unique',self::MODEL_INSERT)
    );

    public $_auto = array(
        array('enable',1,self::MODEL_INSERT,'string')
    );
}
?>