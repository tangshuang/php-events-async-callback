<?php

function debug_log($msg,$append = true) {
    $msg = "[".date('Y-m-d H:i:s')."] $msg\n";
    file_put_contents('./log.txt',$msg,$append ? FILE_APPEND : LOCK_EX);
}