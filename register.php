<?php
session_start();
require_once 'config/database.php';

$page_title = 'Register';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if username exists
    $check_query = "SELECT * FROM users WHERE username = :username";
    $check_stmt = $pdo->prepare($check_query);
    $check_stmt->execute(['username' => $username]);
    
    if($check_stmt->rowCount() > 0) {
        $error = "Username already exists!";
    } else {
        $query = "INSERT INTO users (username, password) VALUES (:username, :password)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['username' => $username, 'password' => $password]);
        
        header('Location: login.php');
        exit();
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="form-container">
        <h2><i class="fas fa-user-plus"></i> Register</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="fas fa-user"></i> Username
                </label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Password
                </label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-user-plus"></i> Register
            </button>
            <p class="text-center mt-3">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>