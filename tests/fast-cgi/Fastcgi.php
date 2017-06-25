<?php
/**
 * Created by PhpStorm.
 * User: yuyi
 * Date: 17/6/24
 * Time: 10:14
 */
class Fastcgi
{
    /**
     * var resource $buffer
     * return by event_buffer_new
     */
    private $buffer;
    private $data;
    private $on_frame;

    public function __construct($buffer, $data)
    {
        $this->data   = $data;
        $this->buffer = $buffer;
    }

    public function onFrame($callback)
    {
        $this->on_frame = $callback;
    }

    public function parse()
    {

    }
}