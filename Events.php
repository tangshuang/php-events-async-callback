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

    /**
     * 判断当前访问是否是一个事件访问
     * @param $event
     * @return mixed
     */
    protected function _is_event($event) {
        return call_user_func(array($this,$event)); // 由于本类是对Events类的扩展，因此，本类也就拥有了Events类的方法
    }

    // ------------------- 下面定义事件，必须为全小写，并返回boolean -------------------

    /**
     * 延时执行
     * @return bool
     */
    public function timeout() {
        return isset($this->headers['event']) && $this->headers['event'] == 'timeout';
    }

    public function backup() {
        return isset($this->headers['event']) && $this->headers['event'] == 'backup';
    }

    public function alipaynotify() {
        // 把支付宝的SDK加载进来
        // 通过支付宝SDK提供的方法，判断回调通知是否为合法的支付宝回调通知，如果是合法的，返回true，否则返回false
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