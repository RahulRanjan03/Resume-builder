<?php
// session_start();
require '../src/database.class.php';
require '../src/function.class.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($fn)) {
    die("Error: Function class is not loaded.");
}

if ($_POST) {
    $post = $_POST;

    if (!empty($post['full_name']) && !empty($post['email_id']) && !empty($post['password'])) {
        $full_name = $db->real_escape_string($post['full_name']);
        $email_id = $db->real_escape_string($post['email_id']);
        $password = md5($db->real_escape_string($post['password']));

        $result = $db->query("SELECT COUNT(*) as user FROM users1 WHERE (email_id='$email_id' && password='$password')");
        $row = $result->fetch_assoc();

        if ($row['user'] > 0) {
            $fn->setError($email_id.' is already registered !.');
            $fn->redirect('../src/register.php');
            die();
        }

        try {
            $db->query("INSERT INTO users1 (full_name, email_id, password) VALUES ('$full_name', '$email_id', '$password')");
            $fn->setAlert('You registered successfully!');
            $fn->redirect('../src/login.php');
            // exit();  
        } catch (Exception $error) {
            $fn->setError("Database error: " . $error->getMessage());
            $fn->redirect('../src/register.php');
            // exit();
        }
    } else {
        $fn->setError('Please fill in all fields.');
        $fn->redirect('../src/register.php');
        exit();
    }
} else {
    $fn->redirect('../src/register.php');
    exit();
}
?>