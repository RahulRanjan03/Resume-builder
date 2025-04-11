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

    if (!empty($post['full_name']) && !empty($post['email_id']) && !empty($post['password'])) {
        $full_name = $db->real_escape_string($post['full_name']);
        $email_id = $db->real_escape_string($post['email_id']);
        $password = password_hash($post['password'], PASSWORD_DEFAULT); // Secure hashing

        // Check if email already exists
        $stmt = $db->prepare("SELECT COUNT(*) as user FROM users1 WHERE email_id = ?");
        $stmt->bind_param("s", $email_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row['user'] > 0) {
            $fn->setError($email_id . ' is already registered!');
            $fn->redirect('../src/register.php');
            exit();
        }

        // Insert new user
        $stmt = $db->prepare("INSERT INTO users1 (full_name, email_id, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $full_name, $email_id, $password);
        if ($stmt->execute()) {
            $fn->setAlert('You registered successfully!');
            $fn->redirect('../src/login.php');
        } else {
            $fn->setError("Database error: " . $db->error);
            $fn->redirect('../src/register.php');
        }
        $stmt->close();
    } else {
        $fn->setError('Please fill in all fields.');
        $fn->redirect('../src/register.php');
    }
} else {
    $fn->redirect('../src/register.php');
}
exit();
?>