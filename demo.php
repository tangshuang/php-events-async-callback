<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require 'debug.functions.php';
require 'Events.php';
require 'EventListener.class.php';

$EventListener = new EventListener('timeout');
$EventListener->add('timeout','setTimeout');
function setTimeout() {
    sleep(10);
    debug_log('回调成功。');
}

debug_log('开始访问。');
echo '这里你可以写很多很多代码，然后在代码执行过程中，通过$EventListener->run调用来触发异步回调。';
$EventListener->run('timeout');