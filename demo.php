<?php

/**
 * 异步回调（延时执行）的演示
 */

// 引入类文件
require 'debug.functions.php';
require 'Events.php';
require 'EventListener.class.php';

debug_error();

/**
 * 实例化事件监听类
 * 构造函数可以传入多个参数，每个参数都是一个事件名称，例如可以$EventListener = new EventListener('timeout','sendmail',...);
 * 如果不传入参数，则不会监听任何事件
 */
$EventListener = new EventListener('timeout');

/**
 * 添加一个回调函数
 * 第一个参数是事件名称，表示该回调函数将会在监听到哪个事件发生时被回调
 * 第二个参数是回调函数名，回调函数写在下方
 */
$EventListener->add('timeout','setTimeout');
function setTimeout() { // 回调函数的具体内容，注意，回调函数没有任何参数
    sleep(10);
    debug_log('回调成功。');
}

/**
 * 正常的逻辑代码，在正常的逻辑代码执行过程中，可以通过调用$EventListener->trigger来触发某个事件的回调
 */
debug_log('开始访问。');
echo '这里你可以写很多很多代码，然后在代码执行过程中，通过$EventListener->trigger调用来触发异步回调。';
$EventListener->trigger('timeout'); // trigger的参数是某个事件名称