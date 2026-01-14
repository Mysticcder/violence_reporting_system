<?php
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function mailer(): PHPMailer {
  $mail = new PHPMailer(true);
  $mail->isSMTP();
  $mail->Host = 'smtp.example.com';
  $mail->SMTPAuth = true;
  $mail->Username = 'your@email.com';
  $mail->Password = 'yourpassword';
  $mail->SMTPSecure = 'tls';
  $mail->Port = 587;
  $mail->setFrom('your@email.com', 'Violence Reporting System');
  return $mail;
}