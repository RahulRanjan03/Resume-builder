<?php
// session_start();
require '../src/database.class.php';
require '../src/function.class.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($fn)) {
    die("Error: Function class is not loaded.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = $_POST;

    if (!empty($post['otp'])) {
        $otp = $post['otp'];

        if ($fn->getSession('otp') == $otp) {
            $fn->setAlert('Email verified successfully!');
            $fn->setSession('otp', null); // Clear OTP after verification
            $fn->redirect('../src/changepassword.php');
        } else {
            $fn->setError('Incorrect OTP.');
            $fn->redirect('../src/verification.php');
        }
    } else {
        $fn->setError('Please enter the 6-digit code sent to your email.');
        $fn->redirect('../src/verification.php');
    }
} else {
    $fn->redirect('../src/verification.php');
}
exit();
?>