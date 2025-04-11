<?php
// session_start();
require '../src/database.class.php';
require '../src/function.class.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($fn)) {
    die("Error: Function class is not loaded.");
}
if (!isset($db) || !$db) {
    die("Error: Database connection failed.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = $_POST;

    if (!empty($post['email_id']) && !empty($post['password'])) {
        $email_id = $db->real_escape_string($post['email_id']);
        $password = $post['password']; // Plain text

        $stmt = $db->prepare("SELECT id, full_name, password FROM users1 WHERE email_id = ?");
        $stmt->bind_param("s", $email_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id']; // Store user ID in session
            $_SESSION['full_name'] = $row['full_name']; // Optional: for display
            $fn->setAuth($row); // Assuming this sets additional auth data
            $fn->setAlert('Logged in!');
            $fn->redirect('../src/myresumes.php');
        } else {
            $fn->setError('Incorrect email ID or password');
            $fn->redirect('../src/login.php');
        }
    } else {
        $fn->setError('Please fill in all fields.');
        $fn->redirect('../src/login.php');
    }
} else {
    $fn->redirect('../src/login.php');
}
exit();
?>