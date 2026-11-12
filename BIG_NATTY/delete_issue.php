<?php 
include 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$issue_id = $_GET['id'] ?? null;

if (!$issue_id) {
    redirect('dashboard.php');
}

$user_id = $_SESSION['user_id'];

// Check if user owns the issue or is admin
$stmt = $pdo->prepare("SELECT * FROM issues WHERE id = ?");
$stmt->execute([$issue_id]);
$issue = $stmt->fetch();

if (!$issue) {
    redirect('dashboard.php');
}

// Only owner or admin can delete
if ($issue['user_id'] != $user_id && !isAdmin()) {
    die("Access denied");
}

// Delete the issue
$stmt = $pdo->prepare("DELETE FROM issues WHERE id = ?");
$stmt->execute([$issue_id]);

// Also delete associated votes
$stmt = $pdo->prepare("DELETE FROM user_votes WHERE issue_id = ?");
$stmt->execute([$issue_id]);

redirect('dashboard.php');
?>
