<?php namespace Wing\Net;
/**
 * Created by PhpStorm.
 * User: yuyi
 * Date: 17/6/25
 * Time: 18:19
 */
class Agent
{
    private $protocol;
    private $ip;
    private $port;
    /**
     * construct
     *
     * @param string $conn
     *  like:
     *      tcp://127.0.0.1:6998
     *      http://127.0.0.1:6998
     *      fastcgi://127.0.0.1:6998
     */
    public function __construct($conn)
    {
        $this->parseConn($conn);
    }

    private function parseConn($conn)
    {
        $temp = explode("://", $conn);
        $this->protocol = strtolower($temp[0]);
        $temp = explode(":", $temp[1]);
        $this->ip   = $temp[0];
        $this->port = $temp[1];
    }

    public function onMessage($callback)
    {
        $response = new Response();
        call_user_func($callback,[$response]);
    }
}