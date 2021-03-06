<?php
//PostMark Email script for sending...:
require('_shared/_sendEmail.php');

//getting the supposidly passed vairables from app
$emailForResend = $_POST["emailToSend"];
$passURLRef = $_POST["passURL"];
$trainNum = $_POST["passTrainNum"];

//********************************************************************
// RESENDING PASS IN EMAIL
//********************************************************************

//having the notification email sent...
$sent = send_email(array(
    'to' => $emailForResend,
    'from' => 'CanTravel <pass@cantravel.co>',
    'subject' => '[Resending] CanTravel Pass - Train ' . $trainNum,
    // 'text_body' => 'Click the link below to download your pass. http://cantravel.co/access/?passID=' . $ourPassID .'&i=' . $i . ' .',
    'html_body' => '<html><body><h1>[Resending] CanTravel Pass - Train ' . $trainNum . '</h1><p><a href="' . $passURLRef . '">Click here to download your personal iOS boarding pass.</a></p><p>You\'re receiving this message because this pass was requested to be resent from the CanTravel mobile app. If you did not request this pass, please notify us via <a href="phil@cantravel.co">email</> ASAP.</p><p><em>CanTravel Team</em></p></body></html>'
), $response, $http_code);
// Did it send successfully?
if( $sent ) {
    echo 'The email was sent!';
} else {
    echo 'The email could not be sent!';
}
// Show the response and HTTP code
echo '<pre>';
echo 'The JSON response from Postmark:<br />';
print_r($response);
echo 'The HTTP code was: ' . $http_code;
echo '</pre>';

?>