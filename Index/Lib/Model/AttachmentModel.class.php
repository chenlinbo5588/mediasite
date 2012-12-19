<?php
class AttachmentModel extends CommonModel {
    public $_validate = array(
        
    );

    public $_auto = array(
        array('createtime','currDateTime',self::MODEL_INSERT,'function'),
    );
}
?>