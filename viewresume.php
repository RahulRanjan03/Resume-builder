<?php
// src/viewresume.php
require 'function.class.php';
$fn->AuthPage();

$resumeId = $_GET['id'] ?? null;
if (!$resumeId || !isset($_SESSION['resumes'][$resumeId])) {
    header("Location: myresumes.php");
    exit();
}

$resume = $_SESSION['resumes'][$resumeId];
$template = $resume['template'] ?? null;

if (!$template) {
    header("Location: myresumes.php");
    exit();
}

// Redirect to the appropriate resume template page with view mode
switch ($template) {
    case 1:
        header("Location: resume1.php?id=$resumeId&mode=view");
        break;
    case 2:
        header("Location: resume2.php?id=$resumeId&mode=view");
        break;
    case 3:
        header("Location: resume3.php?id=$resumeId&mode=view");
        break;
    case 4:
        header("Location: resume4.php?id=$resumeId&mode=view");
        break;
    default:
        header("Location: myresumes.php");
        break;
}
exit();
?>