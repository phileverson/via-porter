<?php

function send_email($email, &$response = null, &$http_code = null) {
    $json = json_encode(array(
        'From' => $email['from'],
        'To' => $email['to'],
        'Cc' => $email['cc'],
        'Bcc' => $email['bcc'],
        'Subject' => $email['subject'],
        'Tag' => $email['tag'],
        'HtmlBody' => $email['html_body'],
        'TextBody' => $email['text_body'],
        'ReplyTo' => $email['reply_to'],
        'Headers' => $email['headers'],
        'Attachments' => $email['attachments']
    ));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://api.postmarkapp.com/email');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Type: application/json',
        'X-Postmark-Server-Token: ' . '76e4cad0-733a-4172-8f32-f6b4cf73dc0a'
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    $response = json_decode(curl_exec($ch), true);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $http_code === 200;
}

?>