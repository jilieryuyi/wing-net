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

    private $agent;

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
        $this->agent = new Tcp($this->ip, $this->port);

        $this->agent->on(Tcp::ON_RECEIVE, [$this, "onMessage"]);
		$this->agent->on(Tcp::ON_WRITE, [$this, "onWrite"]);
		$this->agent->on(Tcp::ON_CONNECT, [$this, "onConnect"]);
		$this->agent->on(Tcp::ON_ERROR, [$this, "onError"]);


    }

    public function start()
	{
		$this->agent->start();
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

    }

    public function onClose()
	{

	}

	public function onConnect()
	{

	}

	public function onWrite()
	{

	}

	public function onError()
	{

	}
}