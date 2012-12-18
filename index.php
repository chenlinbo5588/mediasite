<?php
//header("Location: ./Index/Index");die;
//定义项目名称和路径
define('APP_NAME', 'Index');
define('APP_PATH', './Index/');
define('APP_DEBUG',TRUE);

define('ROOT_PATH',  realpath(dirname(__FILE__)));

/**
 * 修复 swfupload 中固有的 cookie bug 
 */
if(isset($_POST['PHPSESSID'])){
    $_GET['PHPSESSID'] = $_POST['PHPSESSID'];
    $_COOKIE['PHPSESSID'] = $_POST['PHPSESSID'];
}
        
// 加载框架入口文件
require( "./ThinkPHP/ThinkPHP.php");
?>