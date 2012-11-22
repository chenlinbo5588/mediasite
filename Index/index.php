<?php
// 简写DIRECTORY_SEPARATOR
define('DS', DIRECTORY_SEPARATOR);

// 定义网站根目录
define('ROOT_PATH', realpath('..'));

// 定义ThinkPHP框架路径
define('THINK_PATH', ROOT_PATH . DS . 'ThinkPHP/');

//定义项目名称和路径
define('APP_NAME', 'GOO');
//定义项目应用路径

// 加载框架公共入口文件
require(THINK_PATH . DS . "ThinkPHP.php");
// Common/extend.php 文件不存在，我注释掉了
//require(THINK_PATH . DS . "Common/extend.php");
//实例化一个网站应用实例
App::run();
?>