<?php
// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - ApexPlanet</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>📝 ApexPlanet Blog</h1>
            <nav>
                <a href="index.php">Home</a>
                <?php if ($is_logged_in): ?>
                    <a href="create.php">➕ New Post</a>
                    <span>Welcome, <?php echo htmlspecialchars($username); ?> (<?php echo strtoupper($role); ?>)</span>
                    <a href="logout.php">🚪 Logout</a>
                <?php else: ?>
                    <a href="login.php">🔑 Login</a>
                    <a href="register.php">📝 Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main>
        <div class="container">