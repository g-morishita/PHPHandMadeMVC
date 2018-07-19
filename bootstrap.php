<?php
require 'core/ClassLoader.php';

$classLoader = ClassLoader::getInstance(array(dirname(__FILE__) . '/core', dirname(__FILE__) . '/models'));
$classLoader->register();
