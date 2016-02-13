<?php

/**
 * 支付宝支付后回调通知
 */

require 'Events.php';
require 'EventListener.class.php';

$EventListener = new EventListener('alipaynotify');
$EventListener->add('alipaynotify','notify_by_alipay');
function notify_by_alipay() {
    // 执行支付宝回调操作，例如订单更新等
}

// 下面是正常的调起支付流程代码 ----------------
// 创建支付信息时，回调地址设置为$EventListener->current_url();