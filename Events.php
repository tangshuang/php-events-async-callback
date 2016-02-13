<?php

class Events {
    /**
     * 保存head的http请求数据内容，其中包含event
     */
    protected $headers;
    public function __construct() {
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if('HTTP_' == substr($key,0,5)) {
                $key = substr($key,5);
                $key = strtolower($key);
                $headers[$key] = $value;
            }
        }
        $this->headers = $headers;
    }

    /**
     * 未经定义的事件全部返回false
     * @param $function
     * @return bool
     */
    public function __call($function,$params) {
        return false;
    }

    // ------------------- 下面定义事件，必须为全小写，并返回boolean -------------------

    /**
     * 延时执行
     * @return bool
     */
    public function timeout() {
        return isset($this->headers['event']) && $this->headers['event'] == 'timeout';
    }

    /**
     * POST请求
     * @return bool
     */
    public function post() {
        return isset($_POST) && !empty($_POST);
    }

    /**
     * AJAX请求
     * @return bool
     */
    public function ajax() {
        return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest";
    }
}