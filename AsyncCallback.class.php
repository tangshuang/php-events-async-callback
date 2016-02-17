<?php

class AsyncCallback {
    private $auth;

    public function __construct()
    {
        $this->auth = substr(md5('iosnae23a9a~40^33fsdf.adsfie*'),8,16);

        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if('HTTP_' == substr($key,0,5)) {
                $key = substr($key,5);
                $key = strtolower($key);
                $headers[$key] = $value;
            }
        }
        $this->headers = $headers;

        if(isset($this->headers['auth']) && $this->headers['auth'] == $this->auth)
        {
            ignore_user_abort();
            set_time_limit(0);

            $event = $this->headers['event'];
            $data = $this->headers['data'];
            parse_str($data,$data);
            call_user_func(array($this,$event.'Callback'),$data);
            exit;
        }
    }

    public function setTimeout($function,$timeout)
    {
        $this->sock('setTimeout',array('function' => $function,'timeout' => $timeout));
    }
    private function setTimeoutCallback($data) {
        $function = $data['function'];
        $timeout = $data['timeout'];
        sleep($timeout);
        call_user_func($function);
    }


    private function sock($event,$data) {
        $url = $this->current_url();
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

        $data = http_build_query($data);

        $fp = fsockopen($host,$port,$errno,$errstr,1);
        if(!$fp) {
            return false;
        }
        stream_set_blocking($fp,0);
        stream_set_timeout($fp,1);
        $header = "GET $path  / HTTP/1.1\r\n";
        $header .= "Host: $host\r\n";
        $header .= "Event: $event\r\n";
        $header .= "Data: $data\r\n";
        $header .= "Auth: {$this->auth}\r\n";
        $header .= "Connection: Close\r\n\r\n";
        fwrite($fp,$header);
        fclose($fp);
        return true;
    }
    private function current_url() {
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