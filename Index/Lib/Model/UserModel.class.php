<?php
class UserModel extends CommonModel {
    public $_validate = array(
        array('account','/^[a-z]\w{3,}$/i','account is not valid'),
        array('password','require','password must'),
        array('account','','account already exists',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
        array('nickname','require','nickname must')
    );

    public $_auto = array(
        array('createtime','currDateTime',self::MODEL_INSERT,'function'),
        array('updatetime','currDateTime',self::MODEL_UPDATE,'function'),
        array('enable',1,self::MODEL_INSERT)
    );
}
?>