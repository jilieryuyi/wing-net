<?php namespace Wing\Net;
/**
 * Created by PhpStorm.
 * User: yuyi
 * Date: 17/3/13
 * Time: 11:55
 */
class Http
{
    const ON_HTTP_RECEIVE = "on_http_msg";

    protected $http_host;
    protected $http_port;

    public function __construct($ip = "0.0.0.0", $port = 9998)
    {
        parent::__construct($ip, $port);
        $this->on(self::ON_WRITE,   [$this, "onWrite"]);
        $this->on(self::ON_RECEIVE, [$this, "onReceive"]);
    }

    //http协议在发送后要关闭连接
    public function onWrite($client, $buffer)
    {
        $this->free($client);
    }

    public function onReceive($client, $buffer, $data)
    {
        $this->call(self::ON_HTTP_RECEIVE, [$client, new HttpResponse($buffer, $data)]);
    }

    public function send($buffer, $data)
    {
        event_buffer_write($buffer,$data);
    }
}