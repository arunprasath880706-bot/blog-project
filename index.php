<?php
require_once 'config/database.php';

// Fetch posts with user info (USING PREPARED STATEMENT)
$stmt = $pdo->prepare("SELECT p.*, u.username FROM posts p 
                       LEFT JOIN users u ON p.user_id = u.id 
                       ORDER BY p.created_at DESC");
$stmt->execute();
$posts = $stmt->fetchAll();

include 'includes/header.php';
?>

<h2>📚 All Blog Posts</h2>

<?php if (isset($_GET['deleted'])): ?>
    <div class="success">✅ Post deleted successfully!</div>
<?php endif; ?>

<?php if (empty($posts)): ?>
    <p>No posts yet. Be the first to create one!</p>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
            <p class="post-meta">
                By: <?php echo htmlspecialchars($post['username']); ?> | 
                Date: <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
            </p>
            <p><?php echo htmlspecialchars(substr($post['content'], 0, 200)) . '...'; ?></p>
            
            <div class="post-actions">
                <a href="edit.php?id=<?php echo $post['id']; ?>">✏️ Edit</a>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <a href="delete.php?id=<?php echo $post['id']; ?>" 
                       onclick="return confirm('Are you sure you want to delete this post?')">🗑️ Delete</a>
                <?php endif; ?>
            </div>
        </div>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>