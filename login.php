<?php
require_once 'config/database.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim(htmlspecialchars($_POST['username']));
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        $errors[] = "Username and password are required";
    }
    
    // Check credentials (USING PREPARED STATEMENT)
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            // Login successful - store user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect to index
            header('Location: index.php');
            exit();
        } else {
            $errors[] = "Invalid username or password";
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-form">
    <h2>🔑 Login</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p class="error">❌ <?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Login</button>
    </form>
    
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include 'includes/footer.php'; ?>