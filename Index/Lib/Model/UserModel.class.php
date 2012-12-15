<?php
class UserModel extends CommonModel {
    public $_validate = array(
        array('account','/^[a-z]\w{3,}$/i','account is not valid'),
        array('password','require','password must'),
        array('account','','account already exists',self::EXISTS_VAILIDATE,'unique',self::MODEL_INSERT)
    );

    public $_auto = array(
        array('createtime','time',self::MODEL_INSERT,'function')
    );
}
?>