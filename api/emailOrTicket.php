<?php
require_once "lib/email/lib/PHPMailer/PHPMailerAutoload.php";
require_once "config/app.php";
require_once "lib/Common.php";

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

$fileName = "EMAIL_SENT_" . (string)date("Y_m_d_A", time()) . ".txt";

// E-mail recipient
// $emailRecipients = array(
//     'tamjid@ssd-tech.io' => 'Md. Tamjid Morsalin',
// );
// $ccEmail = 'raqibul.hasan@ssd-tech.io';

// $emailRecipients = 'support@ghoorilearning.com';
$postData = file_get_contents("php://input");
$postData = json_decode($postData, true);

// $emailSender = $_POST['email'];
// $query = $_POST['query'];

$emailSender = $postData['email'];
$query = $postData['query'];

$logTxt = $emailSender .$logSeparator .$query . $logSeparator;

$body = <<<HTML
        <p>$query</p>
HTML;

$mailResp = sendMail($emailRecipients, $subject, $body, $emailSender);

$logTxt .= json_encode($mailResp, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . $logSeparator ;

$objCommon = new Common();
$objCommon->logWrite($fileName, $logTxt);

echo json_encode($mailResp);

function sendMail($toAddresses, $subject, $body, $fromAddress)
{
    // global $ccEmail;

    $mail = new PHPMailer;
    $mail->CharSet = "UTF-8";
    $mail->SMTPDebug = 0; // Enable verbose debug output

    // $mail->isSMTP(); // Set mailer to use SMTP
    $mail->Host = MAIL_LOCAL_DOMAIN; // Specify main and backup SMTP servers
    $mail->SMTPAuth = SMTP_AUTH; // Enable SMTP authentication
    $mail->Username = SMTP_USER; // SMTP username
    $mail->Password = SMTP_PSWD; // SMTP password
    $mail->SMTPSecure = true; // Enable TLS encryption, `ssl` also accepted
    $mail->Port = SMTP_PORT; // TCP port to connect to
    $mail->setFrom($fromAddress);
    foreach ($toAddresses as $email => $name) {
        $mail->AddAddress($email, $name);
    }
    // $mail->addAddress($to);
    // $mail->addCC($ccEmail);
    $mail->isHTML(true); // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = $body;
    $r = $mail->send();
    
    if (!$r) {
        return array(
            'status' => 'error',
            'msg' => $mail->ErrorInfo
        );
    } else {
        return array(
            'status' => 'success',
            'msg' => 'Email has been sent'
        );
    }

    // return $r;
}