<?php
require('common/function.php');
define('IA_ROOT', str_replace("\\", '/', dirname(__FILE__)));
if (!file_exists(IA_ROOT . '/data/install.lock')) {
    header('location: ./install.php');
    exit;
} 
header('location:/index.html');
