<?php

function debug_log($msg,$append = true) {
    $msg = "[".date('Y-m-d H:i:s')."] $msg\n";
    file_put_contents('./runtime/log.txt',$msg,$append ? FILE_APPEND : LOCK_EX);
}

function debug_error() {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}