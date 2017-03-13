<?php
/**
 * Created by PhpStorm.
 * User: yuyi
 * Date: 17/3/13
 * Time: 11:27
 */
include __DIR__."/../src/Library/Tcp.php";
include __DIR__."/../src/Library/HttpData.php";
include __DIR__."/../src/Library/Http.php";

use Wing\Net\Http;
use Wing\Net\HttpData;

$server = new Http();

$server->on(Http::ON_CONNECT, function($socket, $buffer, $id){
    echo "新的连接\r\n";
});

$server->on(Http::ON_HTTP_RECEIVE, function(
    $socket,
    $buffer,
    $id,
    HttpData $data,
    $content
) use($server) {

    echo "收到消息\r\n";

    $response_content   =
        //http方法
        $data->getMethod()."<br/><br/>".
        //请求的资源
        $data->getResource()."<br/><br/>".
        //所有的get参数
        json_encode($data->getAll())."<br/><br/>".
        //http协议版本
        $data->getProtocol()."<br/><br/>".
        //http host
        $data->getHost()."<br/><br/>".
        //http port
        $data->getPort()."<br/><br/>".
        //所有的headers
        json_encode($data->getHeaders())."<br/><br/>".
        //所有的cookies
        json_encode($data->getCookies())."<br/><br/>".
        //所有的accept
        json_encode($data->getAccepts())."<br/><br/>".

        "<br/><br/><br/><br/>".
        //将请求的数据原封不动的输出
        str_replace("\n","<br/>",$content);

    //输出http headers
    $headers            = [
        "HTTP/1.1 200 OK",
        "Connection: Close",
        "Server: wing-http",
        "Date: " . gmdate("D,d M Y H:m:s")." GMT",
        "Content-Type: text/html",
        "Content-Length: " . strlen($response_content)
    ];

    $server->send($buffer,
        implode("\r\n",$headers)."\r\n\r\n".$response_content
    );
});

$server->on(Http::ON_WRITE, function($socket, $buffer, $id){
    echo "发送成功\r\n";
});

$server->on(Http::ON_ERROR, function($socket, $buffer, $id, $error){
    echo "发生错误\r\n";
});

$server->start();