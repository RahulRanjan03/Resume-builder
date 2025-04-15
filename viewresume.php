<?php
require 'function.class.php';
require '../src/database.class.php';


$fn->AuthPage();
$userId = $_SESSION['user_id'] ?? 0;

$resumeId = $_GET['id'] ?? null;
if (!$resumeId) {
    header("Location: myresumes.php");
    exit();
}

// Fetch resume data
$stmt = $db->prepare("SELECT template FROM resumes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $resumeId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$resume = $result->fetch_assoc();
$stmt->close();

if (!$resume || !$resume['template']) {
    header("Location: myresumes.php");
    exit();
}

// Redirect to the appropriate resume template page
switch ($resume['template']) {
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
        case 5:
            header("Location: resume5.php?id=$resumeId&mode=view");
            break;
        case 6:
            header("Location: resume6.php?id=$resumeId&mode=view");
            break;
        case 7:
            header("Location: resume7.php?id=$resumeId&mode=view");
            break;
        case 8:
            header("Location: resume8.php?id=$resumeId&mode=view");
            break;
    default:
        header("Location: myresumes.php");
        break;
}
exit();
?>