<?php

/**
 * Class EventListener
 * async callback
 */

class EventListener extends Events {
    /**
     * 用于记录事件及其监听回调的文件，注意，该文件不要被访客直接访问
     */
    private $file;

    /**
     * 用于设置私钥
     * @var
     */
    private $auth = 'sdfjadsfasodi98ds87f89ds6';

    /**
     * 构造
     * EventListener constructor.
     */
    public function __construct() {
        parent::__construct();
        //$this->file = sys_get_temp_dir().'/events/'.md5($this->_current_url()).'.events'; // 串口事件文件
        $this->file = './'.md5($this->_current_url()).'.events';

        if(func_num_args() > 0) {
            $events = func_get_args();
        }
        else {
            $events = array();
            $events_functions = $this->_get_events();
            if(is_array($events_functions) && !empty($events_functions)) foreach($events_functions as $event => $functions) {
                $events[] = $event;
            }
        }

        // 如果是内部请求，也就是通过下面的sock发起的异步请求，那么使页面在断开访问后，仍然能够执行
        if(isset($this->headers['event']) && isset($this->headers['auth']) && $this->headers['auth'] == md5($this->auth)) {
            // 为非阻塞做准备，当非阻塞请求发出时，下面两句可以保证程序正常执行
            ignore_user_abort();
            set_time_limit(0);
        }

        if(is_array($events) && !empty($events)) {
            /*
            $flag = false;
            foreach($events as $event) {
                if($this->_is_event($event)) {
                    // 为非阻塞做准备，当非阻塞请求发出时，下面两句可以保证程序正常执行
                    if($flag === false) {
                        ignore_user_abort();
                        set_time_limit(0);
                    }
                    $this->tigger($event);
                    $flag = true;
                }
            }
            if($flag)
                exit;
            */

            // 上面注释掉的代码，会执行所有的事件回调，为了保证一个页面仅完成一个任务，本类仅允许每一次回调，只执行一个事件的回调，由传入的事件顺序决定
            foreach($events as $event) {
                if($this->_is_event($event)) {
                    $this->tigger($event);
                    exit;
                }
            }
        }

        // 如果是内部请求，也就是通过下面的sock发起的异步请求，那么在执行完上面的触发之后，要执行退出程序的操作，否则容易造成死循环
        if(isset($this->headers['event']) && isset($this->headers['auth']) && $this->headers['auth'] == md5($this->auth)) {
            exit;
        }
    }

    /**
     * 判断当前访问是否是一个事件访问
     * @param $event
     * @return mixed
     */
    private function _is_event($event) {
        return call_user_func(array($this,$event)); // 由于本类是对Events类的扩展，因此，本类也就拥有了Events类的方法
    }

    /**
     * 读取文件内容
     * @return bool|string
     */
    private function _read_events() {
        if(!file_exists($this->file))
            return false;
        return file_get_contents($this->file);
    }

    /**
     * 写入文件内容
     * @param $content
     * @param bool $append
     * @return int
     */
    private function _write_events($content,$append = false) {
        return file_put_contents($this->file,$content,$append ? FILE_APPEND : LOCK_EX);
    }

    /**
     * 读取所有事件监听绑定
     * @return bool|mixed|string
     */
    private function _get_events() {
        $events = $this->_read_events();
        if($events)
            $events = unserialize($events);
        return $events;
    }

    /**
     * 读取单个事件监听绑定
     * @param $event
     * @return bool
     */
    private function _get_event($event) {
        $events = $this->_get_events();
        if(!isset($events[$event]))
            return false;
        return $events[$event];
    }

    /**
     * 保存所有事件监听绑定
     * @param $events
     * @return bool|int
     */
    private function _set_events($events) {
        if(!is_array($events) || empty($events))
            return false;
        $events = serialize($events);
        return $this->_write_events($events);
    }


    /**
     * 添加一个监听绑定
     * @param $event
     * @param $function
     * @return bool|int
     */
    private function _add_event($event,$function) {
        if(!is_string($event) && !is_string($function))
            return false;
        $events = $this->_get_events();
        if(!isset($events[$event]))
            $events[$event] = array();
        if(in_array($function,$events[$event]))
            return true;
        $events[$event][] = $function;
        return $this->_set_events($events);
    }

    /**
     * 移除一个监听绑定
     * @param $event
     * @param $function
     * @return bool|int
     */
    private function _remove_event($event,$function) {
        if(!is_string($event) && !is_string($function))
            return false;
        $events = $this->_get_events();
        if(!isset($events[$event]))
            return true;
        if(!in_array($function,$events[$event]))
            return true;
        $key = array_search($function,$events[$event]);
        unset($events[$event][$key]);
        return $this->_set_events($events);
    }

    /**
     * 销毁一个监听事件的所有绑定
     * @param $event
     */
    private function _destory_event($event) {
        $events = $this->_get_events();
        if(isset($events[$event]))
            unset($events[$event]);
        $this->_set_events($events);
    }

    /**
     * 销毁所有监听事件绑定
     */
    private function _destory_events() {
        $this->_set_events(null);
    }

    /**
     * 获取事件监听绑定内容
     * @param bool $event 为false时表示获取全部内容，而设置了某值时，仅获取对应的事件的绑定内容
     * @return bool|mixed|string
     */
    public function get($event = false) {
        if(!$event)
            return $this->_get_events();
        return $this->_get_event($event);
    }

    /**
     * 绑定一个回调函数到某个事件
     * @param $event
     * @param $function
     * @return bool|int
     */
    public function add($event,$function) {
        return $this->_add_event($event,$function);
    }

    /**
     * 从某个事件的回调函数中删除该回调函数
     * @param $event
     * @param $function
     * @return bool|int
     */
    public function remove($event,$function) {
        return $this->_remove_event($event,$function);
    }

    /**
     * 模拟触发某个事件
     * @param $event
     */
    public function tigger($event) {
        $functions = $this->_get_event($event);
        if(is_array($functions) && !empty($functions)) foreach($functions as $function) {
            call_user_func($function);
        }
    }

    /**
     * 触发异步回调，也就是通知另外一个支进程可以进行了，
     * @param $event
     * @param array $options
     */
    public function run($event) {
        $url = $this->_current_url();
        $host = parse_url($url,PHP_URL_HOST);
        $path = parse_url($url,PHP_URL_PATH);
        $query = parse_url($url,PHP_URL_QUERY);
        if($query)
            $path .= '?'.$query;
        $port = parse_url($url,PHP_URL_PORT);
        $port = $port ? $port : 80;
        $scheme = parse_url($url,PHP_URL_SCHEME);
        if($scheme == 'https')
            $host = 'ssl://'.$host;

        $fp = fsockopen($host,$port,$errno,$errstr,1);
        if(!$fp) {
            return false;
        }
        stream_set_blocking($fp,0);
        stream_set_timeout($fp,1);
        $header = "GET $path  / HTTP/1.1\r\n";
        $header .= "Host: $host\r\n";
        $header .= "Event: $event\r\n";
        $header .= "Auth: ".md5($this->auth)."\r\n";
        $header .= "Connection: Close\r\n\r\n";
        fwrite($fp,$header);
        fclose($fp);
        return true;
    }

    /**
     * 获取当前访问页面的完整url
     * @return mixed
     */
    private function _current_url() {
        $url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if(!isset($_SERVER['HTTPS']))
            $url = 'http://'.$url;
        elseif($_SERVER['HTTPS'] === 1 || $_SERVER['HTTPS'] === 'on' || $_SERVER['SERVER_PORT'] == 443)
            $url = 'https://'.$url;
        else
            $url = 'http://'.$url;
        return $url;
    }

}