<?php

$fromMail = 'raqibul.hasan@ssd-tech.io';

$headers = 'From: ' . $fromMail . "\r\n" .
    'Reply-To: ' . $fromMail . "\r\n" .
    'MIME-Version: 1.0' . "\r\n" .
    'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$toEmail = 'omar@ssd-tech.io';

$subject = 'Test Email';
$message = "Test Message";
$result = mail($toEmail, $subject, $message, $headers);

if ($result) {
    echo 'Mail Sent.';
} else {
    echo 'Mail not sent.';
}