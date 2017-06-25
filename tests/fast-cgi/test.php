<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/21
 * Time: 16:23
 */
include __DIR__."/Tcp.php";

define('FCGI_VERSION_1', 1);
define('FCGI_BEGIN_REQUEST', 1);
define('FCGI_RESPONDER', 1);
define('FCGI_END_REQUEST', 3);
define('FCGI_PARAMS', 4);
define('FCGI_STDIN', 5);
define('FCGI_STDOUT', 6);
define('FCGI_STDERR', 7);

$tcp = new Tcp("127.0.0.1", 6998);

$tcp->on(Tcp::ON_RECEIVE, function($client, $buffer, $data){


	$headerFormat = 'Cversion/Ctype/nrequestId/ncontentLength/CpaddingLength/x';
	//$record       = unpack($headerFormat, substr($data,0,8));
    //0-8是fastcgi'的requestbegin的header信息
	//8-16是padding包 忽略
    //16-24是params的header信息
	$arr          = unpack($headerFormat, substr($data,16,8));
    $resquestid   = $arr["requestId"];
	$content_len  = $arr["contentLength"];
	$start        = 24;
	$back         = "";

	while ($start < $content_len)
	{
		$flag = substr(sprintf("%08b",(ord(substr($data, $start, 1)))),0,1);

		if ($flag == "0") {
			$name_len = unpack("C", substr($data, $start, 1))[1];
			$start   += 1;
		} else {
			$temp  = unpack("C4", substr($data, $start, 4));
			$B3    = $temp[1];
			$B2    = $temp[2];
			$B1    = $temp[3];
			$B0    = $temp[4];

			$name_len = (($B3 & 0x7f) << 24) + ($B2 << 16) + ($B1 << 8) + $B0;
			$start   +=4;
		}

		$key  = substr($data, $start+1, $name_len);
		$flag = substr(sprintf("%08b",(ord(substr($data, $start, 1)))),0,1);

		if ($flag == "0") {
			$value_len = unpack("C", substr($data, $start, 1))[1];
			$start     += 1;
		} else {
			$temp  = unpack("C4", substr($data, $start, 4));
			$B3    = $temp[1];
			$B2    = $temp[2];
			$B1    = $temp[3];
			$B0    = $temp[4];

			$value_len = (($B3 & 0x7f) << 24) + ($B2 << 16) + ($B1 << 8) + $B0;
			$start    +=4;
		}

		$start += $name_len;
		$value  = substr($data, $start, $value_len);
		$start += $value_len;

		echo $key ,"===>" , $value,"\r\n";

		$back .= $key."====>".$value."<br/>";
	}


	$back_data     = "Status: 200 OK\r\nContent-Type: text/html\r\nContent-Length:".strlen($back)."\r\n\r\n".$back;
	$contentLength = strlen($back_data);
	$header        = pack('CCnnxx', FCGI_VERSION_1, FCGI_STDOUT, $resquestid, $contentLength);

	event_buffer_write($buffer, $header.$back_data);

});

$tcp->on(TCP::ON_WRITE,function($client, $buffer){
	fclose($client);
	event_buffer_free($buffer);
});

$tcp->start();