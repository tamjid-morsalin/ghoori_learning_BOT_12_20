<?php
require_once "./lib/PHPMailer/PHPMailerAutoload.php";

date_default_timezone_set('Asia/Dhaka');
error_reporting(E_ERROR | E_PARSE);
error_reporting(0); // Turn off all error reporting

// Email Info (changeable)
define('SMTP_PORT', 465); // 587);
define('SMTP_AUTH', TRUE);
define('SMTP_USER', 'support@ghoorilearning.com');
define('SMTP_PSWD', 'gHoori@444');
define('MAIL_FROM', 'support@ghoorilearning.com');
define('MAIL_FROM_NAME', 'Ghoori Learning');
define('MAIL_LOCAL_DOMAIN', 'mail.ghoorilearning.com');

// E-mail recipient
$emailRecipients = array(
    'sarjid@ssd-tech.io' => 'Md. Sarjid Obedy',
    'tamjid@ssd-tech.io' => 'Md. Tamjid Morsalin',
    'omar@ssd-tech.io' => 'Md. Omar Faruk',
    'support@ghoorilearning.com' => 'Ghoori Support'
);
$ccEmail = 'raqibul.hasan@ssd-tech.io';

$subject = "Ghoori Learning | Test Mail | New Test";
$body = <<<HTML
        <p>Hello from nowhere</p>
HTML;

$mailResp = sendMail($emailRecipients, $subject, $body);

$frontResp = array();
if ($mailResp) {

    $msg = "Successful.";
    $frontResp['status'] = 'success';
    $frontResp['code'] = 200;
    $frontResp['msg'] = $msg;
} else {

    $msg = "Something went wrong. Please try later.";
    $frontResp['status'] = 'success';
    $frontResp['code'] = 400;
    $frontResp['msg'] = $msg;
}

print "<pre>";
print_r($frontResp);
print "</pre>";

function sendMail($toAddresses, $subject, $body)
{
    global $ccEmail;

    $mail = new PHPMailer;
    $mail->SMTPDebug = 0; // Enable verbose debug output

    // $mail->isSMTP(); // Set mailer to use SMTP
    $mail->Host = MAIL_LOCAL_DOMAIN; // Specify main and backup SMTP servers
    $mail->SMTPAuth = SMTP_AUTH; // Enable SMTP authentication
    $mail->Username = SMTP_USER; // SMTP username
    $mail->Password = SMTP_PSWD; // SMTP password
    $mail->SMTPSecure = true; // Enable TLS encryption, `ssl` also accepted
    $mail->Port = SMTP_PORT; // TCP port to connect to
    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    foreach ($toAddresses as $email => $name) {
        $mail->AddAddress($email, $name);
    }
    // $mail->addAddress($to);
    $mail->addCC($ccEmail);
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = $body;
    $r = $mail->send();
    if (!$r) {
        return json_encode(array(
            'status' => 'error',
            'msg' => $mail->ErrorInfo
        ));
    } else {
        return json_encode(array(
            'status' => 'success',
            'msg' => 'Email has been sent'
        ));
    }

    return $r;
}