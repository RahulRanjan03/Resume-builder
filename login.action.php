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

    if (!empty($post['email_id']) && !empty($post['password'])) {
        $email_id = $db->real_escape_string($post['email_id']);
        $password = md5($db->real_escape_string($post['password']));

        $result = $db->query("SELECT id,full_name FROM users1 WHERE (email_id='$email_id' && password='$password')");
        $row = $result->fetch_assoc();

        if ($row) 
        {
            
            $fn->setAuth($result);
            $fn->setAlert('logged in!');
            $fn->redirect('../src/myresumes.php');
            
        }
        else
        {
            $fn->setError('Incorrect email id or password');
            $fn->redirect('../src/login.php');
        }

        
    } else {
        $fn->setError('Please fill in all fields.');
        $fn->redirect('../src/login.php');
        exit();
    }
} else {
    $fn->redirect('../src/login.php');
    exit();
}
?>