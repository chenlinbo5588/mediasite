<?php
$siteConfig         = array();
$siteConfigFile     = ROOT_PATH . DS . 'config.inc.php';
if(is_file($siteConfigFile)) {
    $siteConfig = require $siteConfigFile;
}
return array_merge($siteConfig, $appConfig);
?>