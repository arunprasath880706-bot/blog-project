<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Create - Add new post
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    $sql = "INSERT INTO posts (title, content) VALUES (:title, :content)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['title' => $title, 'content' => $content]);
    $success = "Post added successfully!";
}

// Delete post
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM posts WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    header('Location: dashboard.php');
    exit();
}

// Get all posts
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 20px auto; padding: 20px; }
        .post { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .post h3 { margin-top: 0; }
        .btn { display: inline-block; padding: 5px 10px; margin: 2px; text-decoration: none; border-radius: 3px; }
        .btn-edit { background: #ffc107; color: black; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-add { background: #28a745; color: white; padding: 10px 20px; }
        .logout { float: right; background: #dc3545; color: white; padding: 5px 15px; text-decoration: none; border-radius: 3px; }
        .container { overflow: hidden; }
        .success { color: green; }
        .form-group { margin: 10px 0; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Blog Dashboard</h2>
        <a href="logout.php" class="logout">Logout</a>
        <p>Welcome, <?php echo $_SESSION['username']; ?>!</p>
        <hr>
    </div>
    
    <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
    
    <!-- Add Post Form -->
    <h3>Add New Post</h3>
    <form method="POST">
        <div class="form-group">
            <input type="text" name="title" placeholder="Post Title" required>
        </div>
        <div class="form-group">
            <textarea name="content" rows="5" placeholder="Post Content" required></textarea>
        </div>
        <button type="submit" name="add_post" class="btn btn-add">Add Post</button>
    </form>
    
    <hr>
    
    <!-- Display Posts -->
    <h3>All Posts</h3>
    <?php if(count($posts) > 0): ?>
        <?php foreach($posts as $post): ?>
            <div class="post">
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                <small>Posted on: <?php echo $post['created_at']; ?></small>
                <br><br>
                <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-edit">Edit</a>
                <a href="?delete=<?php echo $post['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No posts yet. Add your first post above!</p>
    <?php endif; ?>
</body>
</html>