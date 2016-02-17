<?php

require 'AsyncCallback.class.php';
$AsyncCallback = new AsyncCallback();

file_put_contents('./runtime/setTimeout.txt','['.date('Y-m-d H:i:s').']['.microtime().'] 我先处理一点事情',LOCK_EX);

$AsyncCallback->setTimeout('setTimeout',10);
function setTimeout() {
    file_put_contents('./runtime/setTimeout.txt',"\n".'['.date('Y-m-d H:i:s').']['.microtime().'] 我是延时执行的结果',FILE_APPEND);
}

file_put_contents('./runtime/setTimeout.txt',"\n".'['.date('Y-m-d H:i:s').']['.microtime().'] 我再处理一点事情，看看是否有时间差距',FILE_APPEND);