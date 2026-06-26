<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "DELETE FROM posts WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $id]);

header('Location: index.php');
exit();
?>