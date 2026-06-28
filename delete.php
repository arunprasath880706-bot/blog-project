<?php
require_once 'config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("❌ You don't have permission to delete posts.");
}

$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    die("Post ID not provided.");
}

// Delete post (USING PREPARED STATEMENT)
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$post_id]);

// Redirect back to index
header('Location: index.php?deleted=1');
exit();
?>