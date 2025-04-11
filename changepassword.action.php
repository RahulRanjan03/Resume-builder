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

    if (!empty($post['password'])) {
        $password = $db->real_escape_string($post['password']);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Use password_hash() instead of md5
        $email_id = $fn->getSession('email_id'); // Retrieve email from session

        if (!$email_id) {
            $fn->setError('No email found in session. Please start the password reset process again.');
            $fn->redirect('../src/forgotpassword.php');
            exit();
        }

        // Check if the email exists in the database
        $check = $db->query("SELECT id FROM users1 WHERE email_id='$email_id'");
        if ($check->num_rows === 0) {
            $fn->setError('This email is not registered.');
            $fn->redirect('../src/changepassword.php');
            exit();
        }

        // Update the password
        $result = $db->query("UPDATE users1 SET password='$hashed_password' WHERE email_id='$email_id'");
        if ($result && $db->affected_rows > 0) {
            $fn->setAlert('Password changed successfully!');
            $fn->redirect('../src/login.php');
        } else {
            $fn->setError('Failed to update password: ' . $db->error);
            $fn->redirect('../src/changepassword.php');
        }
        exit();
    } else {
        $fn->setError('Please enter your new password.');
        $fn->redirect('../src/changepassword.php');
        exit();
    }
} else {
    $fn->redirect('../src/changepassword.php');
    exit();
}
?>