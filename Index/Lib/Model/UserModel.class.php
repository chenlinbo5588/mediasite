<?php
class UserModel extends CommonModel {
    public $_validate = array(
        array('account','/^[a-z]\w{3,}$/i','帐号格式错误'),
        array('password','require','密码必须'),
        array('account','','帐号已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_INSERT)
    );

    public $_auto = array(
        array('createtime','time',self::MODEL_INSERT,'function')
    );
}
?>