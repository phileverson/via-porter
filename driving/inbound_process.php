<?php

require_once 'postmark-inbound-php-master/lib/Postmark/Autoloader.php';
\Postmark\Autoloader::register();

// this file should be the target of the callback you set in your postmark account
$inbound = new \Postmark\Inbound(file_get_contents('inbound.json'));
//$inbound = new \Postmark\Inbound(file_get_contents('php://input'));

echo $inbound->Subject();
echo $inbound->FromEmail();

foreach($inbound->Attachments() as $attachment) {
    echo $attachment->Name;
    echo $attachment->ContentType;
    $attachment->ContentLength;
    echo ' <img alt="Embedded Image" src="data:image/jpg;base64, ' . $attachment->Content . '" />';
    echo '<br><br><br><br>';
}
?>
