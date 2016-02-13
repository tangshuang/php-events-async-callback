<?php

/**
 * 对进程分支的演示
 */

require 'Events.php';
require 'EventListener.class.php';

$EventListener = new EventListener('backup');
$EventListener->add('backup','backup');
function backup() {
    // 执行备份操作，需要消耗比较多的系统资源和时间
    // 记录执行成功
    $EventListener = new EventListener();
    $EventListener->value('is_success',1);
    $EventListener->value('backup_file_path','...');
}

// 正常的主进程流程
$EventListener->trigger('backup');
// 再执行一些进程，这些代码无需依赖于备份结果
// 对备份结果进行检查
$timeout = 300;
while(1) {
    $is_sucess = $EventListener->value('is_success');
    if($is_sucess == 1)
        break;
    sleep(10);
    $timeout -= 10;
    if($timeout <= 0)
        exit;
}
// 备份成功之后，得到备份文件的路径
$backup_file_path = $EventListener->value('backup_file_path');
// 继续利用这个路径，做后面的处理