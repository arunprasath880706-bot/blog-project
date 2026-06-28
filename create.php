<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim(htmlspecialchars($_POST['title']));
    $content = trim(htmlspecialchars($_POST['content']));
    
    // Server-side validation
    if (empty($title)) {
        $errors[] = "Title is required";
    } elseif (strlen($title) < 5) {
        $errors[] = "Title must be at least 5 characters";
    }
    
    if (empty($content)) {
        $errors[] = "Content is required";
    } elseif (strlen($content) < 10) {
        $errors[] = "Content must be at least 10 characters";
    }
    
    // Insert post (USING PREPARED STATEMENT)
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO posts (title, content, user_id, created_at) VALUES (?, ?, ?, NOW())");
        
        if ($stmt->execute([$title, $content, $_SESSION['user_id']])) {
            $success = "✅ Post created successfully!";
            // Clear form
            $title = $content = '';
        } else {
            $errors[] = "Failed to create post. Please try again.";
        }
    }
}

include 'includes/header.php';
?>

<h2>📝 Create New Post</h2>

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

<form method="POST" action="" id="createForm">
    <div class="form-group">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" 
               value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>"
               required minlength="5">
    </div>
    
    <div class="form-group">
        <label for="content">Content:</label>
        <textarea id="content" name="content" rows="8" 
                  required minlength="10"><?php echo isset($content) ? htmlspecialchars($content) : ''; ?></textarea>
    </div>
    
    <button type="submit">Create Post</button>
</form>

<script>
document.getElementById('createForm').addEventListener('submit', function(e) {
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