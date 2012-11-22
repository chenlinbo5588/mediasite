<?php
return array(
	/* 是否开启调试 */
	'APP_DEBUG'				=> true,
	
	/* URL 模式 */
	'URL_DISPATCH_ON'		=> true,
	'URL_MODEL' 			=> 2,
	
	/* 数据库 */
	'DB_TYPE'				=> 'mysql',
	'DB_HOST'				=> 'localhost', 
	'DB_NAME'				=> 'goo',  
	'DB_USER'				=> 'root', 
	'DB_PWD'				=> '123456',
	'DB_PORT'				=> '3306', 
	'DB_PREFIX'				=> '', 
	
	/* 语言 */
	'LANG_SWITCH_ON'   		=> true,
	'DEFAULT_LANG'      	=> 'zh-cn',                // 默认语言
	'LANG_AUTO_DETECT'  	=> false,                    // 自动侦测语言
	
	/* 表单令牌验证 */
	'TOKEN_ON'				=> true,  // 是否开启令牌验证
	'TOKEN_NAME'			=> '__hash__',    // 令牌验证的表单隐藏字段名称
	'TOKEN_TYPE'			=> 'md5'  //令牌哈希验证规则默认为MD5
);
?>
