<?php
return array(
    /* 是否开启调试 */
    'APP_DEBUG'				=> false,

    /* URL 模式 */
    'URL_DISPATCH_ON'		=> true,
    'URL_MODEL' 			=> 1,

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
    'DEFAULT_LANG'      	=> 'zh-en',                // 默认语言
    'LANG_AUTO_DETECT'  	=> false,                    // 自动侦测语言

    /* 表单令牌验证 */
    'TOKEN_ON'				=> false,  // 是否开启令牌验证
    'TOKEN_NAME'			=> '__hash__',    // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE'			=> 'md5',  //令牌哈希验证规则默认为MD5

    'USER_AUTH_ON'          => true,
    'VAR_PAGE'              => 'page',

    'MEDIA_PLAY_EXT'        => 'avi,mp4,mpg,mpg4,wmv',

    //加密
    'SYSTEM_RSA_NUM'        => '256',
    'SYSTEM_RSA_PUB'        => '116AB',
    'SYSTEM_RSA_PRI'        => '1A1DB84E5A2AAF865DF28C1F4F50BA5016648AC96BE25D50EF0197E865EA7D73',
    'SYSTEM_RSA_MOD'        => '850A4F6550AA07716D231EBD8D1E710AB14DA34206A4AC2825624014F81672C9',

    //邮件配置
    'SYSTEM_EMAIL' => array(
        'SMTP_HOST'   => 'smtp.mail.yahoo.com.cn', //SMTP服务器
        'SMTP_PORT'   => '25', //SMTP服务器端口
        'SMTP_USER'   => 'gooteam@yahoo.cn', //SMTP服务器用户名
        'SMTP_NAME'   => 'Goo Team', //SMTP服务器用户名
        'SMTP_PASS'   => 'test098goo' //SMTP服务器密码
    )
);
?>