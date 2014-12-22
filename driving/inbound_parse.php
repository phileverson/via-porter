<?php

require_once 'postmark-inbound-php-master/lib/Postmark/Autoloader.php';
\Postmark\Autoloader::register();

// this file should be the target of the callback you set in your postmark account
$inbound = new \Postmark\Inbound(file_get_contents('inbound.json'));
//$inbound = new \Postmark\Inbound(file_get_contents('php://input'));

echo $inbound->Subject();
echo $inbound->FromEmail();

echo 'request bin now...';

$result = file_get_contents('http://requestb.in/1lqp0sw1');
echo $result;

?>