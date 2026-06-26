<?php
// ============================================
// CREATE POST
// ============================================

session_start();
require_once 'config/database.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$page_title = 'Create Post';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    if(!empty($title) && !empty($content)) {
        $query = "INSERT INTO posts (title, content) VALUES (:title, :content)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['title' => $title, 'content' => $content]);
        
        header('Location: index.php');
        exit();
    } else {
        $error = "Please fill in all fields.";
    }
}

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="form-container">
        <h2><i class="fas fa-plus-circle"></i> Create New Post</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="title" class="form-label">
                    <i class="fas fa-heading"></i> Title
                </label>
                <input type="text" class="form-control" id="title" name="title" 
                       placeholder="Enter post title..." required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">
                    <i class="fas fa-paragraph"></i> Content
                </label>
                <textarea class="form-control" id="content" name="content" 
                          rows="8" placeholder="Write your post content here..." required></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Publish Post
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>