<?php
/**
 * test.php
 * User: huangxiaoan
 * Created: 2017/6/26 18:02
 * Email: huangxiaoan@xunlei.com
 */
include __DIR__."/Tcp.php";
$tcp = new Tcp("127.0.0.1", 6998);

$tcp->on(TCP::ON_RECEIVE, function($client, $buffer, $data) {
	$arr = unpack("C*",$data);
	foreach ($arr as $v)
		echo chr($v);
});

$tcp->start();