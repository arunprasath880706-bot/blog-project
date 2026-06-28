<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    die("Post ID not provided.");
}

$errors = [];
$success = '';

// Fetch post (USING PREPARED STATEMENT)
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    die("Post not found.");
}

// Check if user has permission (Admin, Editor, or Post Owner)
$is_author = ($post['user_id'] == $_SESSION['user_id']);
$can_edit = ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'editor' || $is_author);

if (!$can_edit) {
    die("❌ You don't have permission to edit this post.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim(htmlspecialchars($_POST['title']));
    $content = trim(htmlspecialchars($_POST['content']));
    
    // Validation
    if (empty($title) || strlen($title) < 5) {
        $errors[] = "Title must be at least 5 characters";
    }
    
    if (empty($content) || strlen($content) < 10) {
        $errors[] = "Content must be at least 10 characters";
    }
    
    // Update post (USING PREPARED STATEMENT)
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        
        if ($stmt->execute([$title, $content, $post_id])) {
            $success = "✅ Post updated successfully!";
            // Refresh post data
            $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
            $stmt->execute([$post_id]);
            $post = $stmt->fetch();
        } else {
            $errors[] = "Failed to update post.";
        }
    }
}

include 'includes/header.php';
?>

<h2>✏️ Edit Post</h2>

<?php if (!empty($errors)): ?>
    <div class="errors">
        <?php foreach ($errors as $error): ?>
            <p class="error">❌ <?php echo $error; ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?php echo $success; ?></div>
<?php endif; ?>

<form method="POST" action="" id="editForm">
    <div class="form-group">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" 
               value="<?php echo htmlspecialchars($post['title']); ?>"
               required minlength="5">
    </div>
    
    <div class="form-group">
        <label for="content">Content:</label>
        <textarea id="content" name="content" rows="8" 
                  required minlength="10"><?php echo htmlspecialchars($post['content']); ?></textarea>
    </div>
    
    <button type="submit">Update Post</button>
</form>

<p><a href="index.php">← Back to Home</a></p>

<script>
document.getElementById('editForm').addEventListener('submit', function(e) {
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;
    
    if (title.length < 5) {
        alert('Title must be at least 5 characters');
        e.preventDefault();
        return false;
    }
    
    if (content.length < 10) {
        alert('Content must be at least 10 characters');
        e.preventDefault();
        return false;
    }
    
    return true;
});
</script>

<?php include 'includes/footer.php'; ?>