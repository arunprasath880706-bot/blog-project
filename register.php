<?php
require_once 'config/database.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize
    $username = trim(htmlspecialchars($_POST['username']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // SERVER-SIDE VALIDATION
    
    // 1. Validate username
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $errors[] = "Username can only contain letters, numbers, and underscores";
    }
    
    // 2. Validate password
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    // 3. Check if passwords match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // 4. Check if username already exists (USING PREPARED STATEMENT)
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() > 0) {
            $errors[] = "Username already exists. Please choose another.";
        }
    }
    
    // 5. If no errors, insert user (USING PREPARED STATEMENT)
    if (empty($errors)) {
        // Hash password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'editor')");
        
        if ($stmt->execute([$username, $hashed_password])) {
            $success = "✅ Registration successful! You can now <a href='login.php'>login here</a>";
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-form">
    <h2>📝 Register</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p class="error">❌ <?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success">
            <?php echo $success; ?>
        </div>
    <?php else: ?>
        <form method="POST" action="" id="registerForm">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       required minlength="3" pattern="[a-zA-Z0-9_]+">
                <small>Minimum 3 characters (letters, numbers, underscore only)</small>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required minlength="6">
                <small>Minimum 6 characters</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit">Register</button>
        </form>
        
        <p>Already have an account? <a href="login.php">Login here</a></p>
    <?php endif; ?>
</div>

<!-- CLIENT-SIDE VALIDATION -->
<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    
    // Username validation
    if (username.length < 3) {
        alert('Username must be at least 3 characters');
        e.preventDefault();
        return false;
    }
    
    // Password validation
    if (password.length < 6) {
        alert('Password must be at least 6 characters');
        e.preventDefault();
        return false;
    }
    
    // Confirm password
    if (password !== confirm) {
        alert('Passwords do not match');
        e.preventDefault();
        return false;
    }
    
    return true;
});
</script>

<?php include 'includes/footer.php'; ?>