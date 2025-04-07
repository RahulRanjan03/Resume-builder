<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
session_start();
require '../src/database.class.php';
require '../src/function.class.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($fn)) {
    die("Error: Function class is not loaded.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = $_POST;

    if (!empty($post['email_id'])) {
        $email_id = $db->real_escape_string($post['email_id']);

        $result = $db->query("SELECT id, full_name FROM users1 WHERE email_id='$email_id'");
        $row = $result->fetch_assoc();

        if ($row) {
            $otp = rand(100000, 999999);
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'montengero4@gmail.com';
                $mail->Password = 'frizusxrkciexzlp';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                // Recipients
                $mail->setFrom('verify@resumebuilder.com', 'Resume Builder');
                $mail->addAddress($email_id);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Forgot Password';
                $mail->Body = 'Your 6 Digit Verification Code is: <b>' . $otp . '</b>';

                $mail->send();
                $fn->setSession('otp', $otp);
                $fn->setSession('email_id', $email_id); // Corrected to setSession
                $fn->redirect('../src/verification.php');
            } catch (Exception $e) {
                $fn->setError('Failed to send OTP: ' . $mail->ErrorInfo);
                $fn->redirect('../src/forgotpassword.php');
            }
        } else {
            $fn->setError('This email is not registered.');
            $fn->redirect('../src/forgotpassword.php');
        }
    } else {
        $fn->setError('Please enter your email ID.');
        $fn->redirect('../src/forgotpassword.php');
    }
} else {
    $fn->redirect('../src/forgotpassword.php');
}
exit();
?>