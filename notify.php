<?php
function sendMailSimple($to, $subject, $body)
{
    $from = "helpdesk.cahul@gmail.com"; // admin / cont SMTP

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: HelpDesk Cahul <{$from}>\r\n";

    return mail($to, $subject, $body, $headers, "-f {$from}");
}